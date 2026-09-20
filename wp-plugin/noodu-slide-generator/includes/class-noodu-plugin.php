<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Noodu_Plugin {
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        add_shortcode( 'noodu_generator', array( $this, 'render_shortcode' ) );
    }

    /* ---------- activation ---------- */

    public static function activate() {
        global $wpdb;

        $upload_dir = wp_upload_dir();
        $noodu_dir  = trailingslashit( $upload_dir['basedir'] ) . 'noodu-slides';
        if ( ! is_dir( $noodu_dir ) ) {
            wp_mkdir_p( $noodu_dir );
        }

        $charset_collate = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $projects = $wpdb->prefix . 'noodu_projects';
        dbDelta(
            "CREATE TABLE $projects (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL DEFAULT 0,
                name varchar(255) NOT NULL DEFAULT '',
                code varchar(50) NOT NULL DEFAULT '',
                prompt longtext,
                model varchar(100) NOT NULL DEFAULT '',
                status varchar(30) NOT NULL DEFAULT 'generating',
                pdf_file varchar(255) NOT NULL DEFAULT '',
                slide_count int(11) NOT NULL DEFAULT 0,
                slides_data longtext,
                created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY  (id),
                KEY user_id (user_id)
            ) $charset_collate;"
        );

        $revisions = $wpdb->prefix . 'noodu_revisions';
        dbDelta(
            "CREATE TABLE $revisions (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                project_id bigint(20) unsigned NOT NULL DEFAULT 0,
                prompt longtext,
                status varchar(30) NOT NULL DEFAULT 'pending',
                created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY  (id),
                KEY project_id (project_id)
            ) $charset_collate;"
        );

        add_option( 'noodu_openai_base_url', 'https://api.openai.com/v1' );
        add_option( 'noodu_openai_api_key', '' );
        add_option( 'noodu_default_model', '' );

        update_option( 'noodu_plugin_version', NOODU_PLUGIN_VERSION );
    }

    public static function deactivate() {
        // Data and settings are kept so reactivating restores everything.
    }

    /* ---------- system checks ---------- */

    public static function check_poppler() {
        if ( ! Noodu_PDF_Generator::exec_available() ) {
            return false;
        }
        $out = @shell_exec( 'command -v pdftotext 2>/dev/null' );
        return ! empty( $out );
    }

    public static function check_browser() {
        return Noodu_PDF_Generator::exec_available() && false !== Noodu_PDF_Generator::find_browser();
    }

    /* ---------- admin menu ---------- */

    public function add_admin_menu() {
        add_menu_page(
            __( 'Noodu Slide Generator', 'noodu-slide-generator' ),
            __( 'Noodu', 'noodu-slide-generator' ),
            'edit_posts',
            'noodu-slide-generator',
            array( $this, 'render_admin_page' ),
            'dashicons-images-alt2',
            25
        );

        add_submenu_page(
            'noodu-slide-generator',
            __( 'Dashboard', 'noodu-slide-generator' ),
            __( 'Dashboard', 'noodu-slide-generator' ),
            'edit_posts',
            'noodu-slide-generator',
            array( $this, 'render_admin_page' )
        );

        add_submenu_page(
            'noodu-slide-generator',
            __( 'Projects', 'noodu-slide-generator' ),
            __( 'Projects', 'noodu-slide-generator' ),
            'edit_posts',
            'noodu-projects',
            array( $this, 'render_projects_page' )
        );

        add_submenu_page(
            'noodu-slide-generator',
            __( 'Settings', 'noodu-slide-generator' ),
            __( 'Settings', 'noodu-slide-generator' ),
            'manage_options',
            'noodu-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'noodu-settings', 'noodu_openai_base_url', array( 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( 'noodu-settings', 'noodu_openai_api_key', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'noodu-settings', 'noodu_default_model', array( 'sanitize_callback' => 'sanitize_text_field' ) );

        add_settings_section(
            'noodu-api-settings',
            __( 'OpenAI API Settings', 'noodu-slide-generator' ),
            array( $this, 'render_api_settings_section' ),
            'noodu-settings'
        );

        add_settings_field( 'noodu_openai_base_url', __( 'API Base URL', 'noodu-slide-generator' ), array( $this, 'render_base_url_field' ), 'noodu-settings', 'noodu-api-settings' );
        add_settings_field( 'noodu_openai_api_key', __( 'API Key', 'noodu-slide-generator' ), array( $this, 'render_api_key_field' ), 'noodu-settings', 'noodu-api-settings' );
        add_settings_field( 'noodu_default_model', __( 'Default Model', 'noodu-slide-generator' ), array( $this, 'render_model_field' ), 'noodu-settings', 'noodu-api-settings' );
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( false === strpos( $hook, 'noodu' ) ) {
            return;
        }

        wp_enqueue_style( 'noodu-admin', NOODU_PLUGIN_URL . 'assets/admin-style.css', array(), NOODU_PLUGIN_VERSION );
        wp_enqueue_script( 'noodu-admin', NOODU_PLUGIN_URL . 'assets/admin-script.js', array(), NOODU_PLUGIN_VERSION, true );

        wp_localize_script(
            'noodu-admin',
            'nooduData',
            array(
                'restUrl'    => esc_url_raw( rest_url( 'noodu/v1/' ) ),
                'nonce'      => wp_create_nonce( 'wp_rest' ),
                'projectsUrl' => admin_url( 'admin.php?page=noodu-projects' ),
                'i18n'       => array(
                    'selectModel'  => __( 'Select a model…', 'noodu-slide-generator' ),
                    'fetching'     => __( 'Fetching…', 'noodu-slide-generator' ),
                    'fetchModels'  => __( 'Fetch available models', 'noodu-slide-generator' ),
                    'generating'   => __( 'Generating…', 'noodu-slide-generator' ),
                    'generate'     => __( 'Generate slides', 'noodu-slide-generator' ),
                    'fillFields'   => __( 'Please fill in the project name, model, and prompt.', 'noodu-slide-generator' ),
                    'modelsFound'  => __( 'models found.', 'noodu-slide-generator' ),
                    'noModels'     => __( 'No models returned. Check the base URL and API key.', 'noodu-slide-generator' ),
                    'done'         => __( 'Deck generated.', 'noodu-slide-generator' ),
                    'confirmDelete' => __( 'Delete this project?', 'noodu-slide-generator' ),
                ),
            )
        );
    }

    public function enqueue_public_scripts() {
        global $post;
        if ( ! is_singular() || ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'noodu_generator' ) ) {
            return;
        }

        wp_enqueue_style( 'noodu-public', NOODU_PLUGIN_URL . 'assets/public-style.css', array(), NOODU_PLUGIN_VERSION );
        wp_enqueue_script( 'noodu-public', NOODU_PLUGIN_URL . 'assets/public-script.js', array(), NOODU_PLUGIN_VERSION, true );

        wp_localize_script(
            'noodu-public',
            'nooduData',
            array(
                'restUrl' => esc_url_raw( rest_url( 'noodu/v1/' ) ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
                'i18n'    => array(
                    'selectModel'   => __( 'Select a model…', 'noodu-slide-generator' ),
                    'fetching'      => __( 'Fetching…', 'noodu-slide-generator' ),
                    'fetchModels'   => __( 'Fetch available models', 'noodu-slide-generator' ),
                    'generating'    => __( 'Generating…', 'noodu-slide-generator' ),
                    'generate'      => __( 'Generate slides', 'noodu-slide-generator' ),
                    'fillFields'    => __( 'Please fill in the project name, model, and prompt.', 'noodu-slide-generator' ),
                    'modelsFound'   => __( 'models found.', 'noodu-slide-generator' ),
                    'noModels'      => __( 'No models returned. Check the base URL and API key.', 'noodu-slide-generator' ),
                    'done'          => __( 'Deck generated.', 'noodu-slide-generator' ),
                    'confirmDelete' => __( 'Delete this project?', 'noodu-slide-generator' ),
                ),
            )
        );
    }

    /* ---------- REST ---------- */

    public function register_rest_routes() {
        register_rest_route( 'noodu/v1', '/models', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'rest_get_models' ),
            'permission_callback' => array( $this, 'check_permission' ),
        ) );

        register_rest_route( 'noodu/v1', '/generate', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'rest_generate' ),
            'permission_callback' => array( $this, 'check_permission' ),
        ) );

        register_rest_route( 'noodu/v1', '/projects', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'rest_list_projects' ),
            'permission_callback' => array( $this, 'check_permission' ),
        ) );

        register_rest_route( 'noodu/v1', '/projects/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'rest_get_project' ),
                'permission_callback' => array( $this, 'check_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'rest_delete_project' ),
                'permission_callback' => array( $this, 'check_permission' ),
            ),
        ) );

        register_rest_route( 'noodu/v1', '/projects/(?P<id>\d+)/revise', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'rest_revise' ),
            'permission_callback' => array( $this, 'check_permission' ),
        ) );
    }

    public function check_permission() {
        return current_user_can( 'edit_posts' );
    }

    private function can_edit_project( $project ) {
        if ( ! $project ) {
            return false;
        }
        return current_user_can( 'manage_options' ) || (int) $project->user_id === get_current_user_id();
    }

    private function make_client( $model = null ) {
        $base_url = get_option( 'noodu_openai_base_url' );
        $api_key  = get_option( 'noodu_openai_api_key' );

        if ( empty( $base_url ) || empty( $api_key ) ) {
            return new WP_Error(
                'noodu_not_configured',
                __( 'OpenAI settings are not configured. Go to Noodu → Settings.', 'noodu-slide-generator' ),
                array( 'status' => 400 )
            );
        }

        return new Noodu_OpenAI_Client( $base_url, $api_key, $model );
    }

    public function rest_get_models( $request ) {
        $client = $this->make_client();
        if ( is_wp_error( $client ) ) {
            return $client;
        }

        try {
            $models = $client->get_models();
        } catch ( Exception $e ) {
            return new WP_Error( 'noodu_api_error', $e->getMessage(), array( 'status' => 502 ) );
        }

        return rest_ensure_response( array( 'success' => true, 'models' => $models ) );
    }

    public function rest_generate( $request ) {
        $project_name = sanitize_text_field( (string) $request->get_param( 'project_name' ) );
        $model        = sanitize_text_field( (string) $request->get_param( 'model' ) );
        $prompt       = wp_kses_post( (string) $request->get_param( 'prompt' ) );

        if ( '' === $project_name || '' === $model || '' === $prompt ) {
            return new WP_Error(
                'noodu_missing_params',
                __( 'Project name, model, and prompt are all required.', 'noodu-slide-generator' ),
                array( 'status' => 400 )
            );
        }

        $client = $this->make_client( $model );
        if ( is_wp_error( $client ) ) {
            return $client;
        }

        $context = array();

        // An uploaded PDF is used as reference material for the prompt.
        $file = $request->get_file_params();
        if ( ! empty( $file['reference_file'] ) ) {
            try {
                $uploader = new Noodu_File_Uploader();
                $upload   = $uploader->upload( 'reference_file' );
                if ( 'application/pdf' === $upload['type'] ) {
                    $text = $uploader->extract_text_from_pdf( $upload['path'] );
                    if ( '' !== $text ) {
                        $context['reference'] = mb_substr( $text, 0, 6000 );
                    }
                }
                $uploader->cleanup( $upload['path'] );
            } catch ( Exception $e ) {
                // A bad reference file should not sink the whole generation.
                $context['reference_error'] = $e->getMessage();
            }
        }

        try {
            $slides_data = $client->generate_slide_content( $prompt, $context );
        } catch ( Exception $e ) {
            return new WP_Error( 'noodu_api_error', $e->getMessage(), array( 'status' => 502 ) );
        }

        $code       = 'noodu-' . wp_generate_password( 8, false, false );
        $project_id = Noodu_Database::create_project(
            array(
                'user_id'     => get_current_user_id(),
                'name'        => $project_name,
                'code'        => $code,
                'prompt'      => $prompt,
                'model'       => $model,
                'status'      => 'generating',
                'slides_data' => $slides_data,
            )
        );

        if ( ! $project_id ) {
            return new WP_Error( 'noodu_db_error', __( 'Could not save the project.', 'noodu-slide-generator' ), array( 'status' => 500 ) );
        }

        $upload_dir = wp_upload_dir();
        $output     = trailingslashit( $upload_dir['basedir'] ) . 'noodu-slides/' . $code;

        $generator = new Noodu_PDF_Generator( $project_name, $code, $output );
        $generator->set_slides( $slides_data );

        if ( 0 === $generator->get_slide_count() ) {
            Noodu_Database::update_project( $project_id, array( 'status' => 'error' ) );
            return new WP_Error(
                'noodu_empty_deck',
                __( 'The model did not return any slides. Try a more specific prompt.', 'noodu-slide-generator' ),
                array( 'status' => 502 )
            );
        }

        try {
            $file_name = $generator->generate();
        } catch ( Exception $e ) {
            Noodu_Database::update_project( $project_id, array( 'status' => 'error' ) );
            return new WP_Error( 'noodu_render_error', $e->getMessage(), array( 'status' => 500 ) );
        }

        Noodu_Database::update_project(
            $project_id,
            array(
                'status'      => 'completed',
                'pdf_file'    => $file_name,
                'slide_count' => $generator->get_slide_count(),
            )
        );

        return rest_ensure_response(
            array(
                'success'      => true,
                'project_id'   => $project_id,
                'name'         => $project_name,
                'code'         => $code,
                'slide_count'  => $generator->get_slide_count(),
                'file'         => $file_name,
                'download_url' => Noodu_Database::file_url( $file_name ),
                'is_pdf'       => ( 'pdf' === strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) ) ),
            )
        );
    }

    public function rest_list_projects( $request ) {
        $user_id  = current_user_can( 'manage_options' ) ? null : get_current_user_id();
        $projects = Noodu_Database::get_projects( $user_id );

        $out = array();
        foreach ( $projects as $project ) {
            $out[] = array(
                'id'           => (int) $project->id,
                'name'         => $project->name,
                'model'        => $project->model,
                'status'       => $project->status,
                'slide_count'  => (int) $project->slide_count,
                'created_at'   => $project->created_at,
                'download_url' => $project->pdf_file ? Noodu_Database::file_url( $project->pdf_file ) : '',
            );
        }

        return rest_ensure_response( array( 'success' => true, 'projects' => $out ) );
    }

    public function rest_get_project( $request ) {
        $project = Noodu_Database::get_project( (int) $request['id'] );
        if ( ! $this->can_edit_project( $project ) ) {
            return new WP_Error( 'noodu_not_found', __( 'Project not found.', 'noodu-slide-generator' ), array( 'status' => 404 ) );
        }

        $project->download_url = $project->pdf_file ? Noodu_Database::file_url( $project->pdf_file ) : '';
        $project->revisions    = Noodu_Database::get_revisions( (int) $project->id );

        return rest_ensure_response( array( 'success' => true, 'project' => $project ) );
    }

    public function rest_delete_project( $request ) {
        $project = Noodu_Database::get_project( (int) $request['id'] );
        if ( ! $this->can_edit_project( $project ) ) {
            return new WP_Error( 'noodu_not_found', __( 'Project not found.', 'noodu-slide-generator' ), array( 'status' => 404 ) );
        }

        Noodu_Database::delete_project( (int) $project->id );

        return rest_ensure_response( array( 'success' => true ) );
    }

    public function rest_revise( $request ) {
        $project = Noodu_Database::get_project( (int) $request['id'] );
        if ( ! $this->can_edit_project( $project ) ) {
            return new WP_Error( 'noodu_not_found', __( 'Project not found.', 'noodu-slide-generator' ), array( 'status' => 404 ) );
        }

        $revision_prompt = wp_kses_post( (string) $request->get_param( 'revision_prompt' ) );
        if ( '' === $revision_prompt ) {
            return new WP_Error( 'noodu_missing_params', __( 'A revision prompt is required.', 'noodu-slide-generator' ), array( 'status' => 400 ) );
        }

        $client = $this->make_client( $project->model );
        if ( is_wp_error( $client ) ) {
            return $client;
        }

        Noodu_Database::add_revision( (int) $project->id, $revision_prompt );

        $combined = sprintf(
            "Here is the current deck as JSON:\n\n%s\n\nRevise it as follows: %s\n\nReturn the complete revised deck in the same JSON shape.",
            $project->slides_data,
            $revision_prompt
        );

        try {
            $slides_data = $client->generate_slide_content( $combined );
        } catch ( Exception $e ) {
            return new WP_Error( 'noodu_api_error', $e->getMessage(), array( 'status' => 502 ) );
        }

        $upload_dir = wp_upload_dir();
        $output     = trailingslashit( $upload_dir['basedir'] ) . 'noodu-slides/' . $project->code;

        $generator = new Noodu_PDF_Generator( $project->name, $project->code, $output );
        $generator->set_slides( $slides_data );

        if ( 0 === $generator->get_slide_count() ) {
            return new WP_Error( 'noodu_empty_deck', __( 'The revision returned no slides.', 'noodu-slide-generator' ), array( 'status' => 502 ) );
        }

        try {
            $file_name = $generator->generate();
        } catch ( Exception $e ) {
            return new WP_Error( 'noodu_render_error', $e->getMessage(), array( 'status' => 500 ) );
        }

        Noodu_Database::update_project(
            (int) $project->id,
            array(
                'status'      => 'completed',
                'pdf_file'    => $file_name,
                'slide_count' => $generator->get_slide_count(),
                'slides_data' => $slides_data,
            )
        );

        return rest_ensure_response(
            array(
                'success'      => true,
                'slide_count'  => $generator->get_slide_count(),
                'download_url' => Noodu_Database::file_url( $file_name ),
            )
        );
    }

    /* ---------- views ---------- */

    public function render_admin_page() {
        include NOODU_PLUGIN_DIR . 'admin/dashboard.php';
    }

    public function render_projects_page() {
        include NOODU_PLUGIN_DIR . 'admin/projects.php';
    }

    public function render_settings_page() {
        include NOODU_PLUGIN_DIR . 'admin/settings.php';
    }

    public function render_api_settings_section() {
        echo '<p>' . esc_html__( 'Works with the OpenAI API or any OpenAI-compatible endpoint.', 'noodu-slide-generator' ) . '</p>';
    }

    public function render_base_url_field() {
        $value = get_option( 'noodu_openai_base_url', 'https://api.openai.com/v1' );
        echo '<input type="url" name="noodu_openai_base_url" value="' . esc_attr( $value ) . '" class="regular-text" placeholder="https://api.openai.com/v1" />';
        echo '<p class="description">' . esc_html__( 'Include the /v1 path.', 'noodu-slide-generator' ) . '</p>';
    }

    public function render_api_key_field() {
        $value = get_option( 'noodu_openai_api_key' );
        echo '<input type="password" name="noodu_openai_api_key" value="' . esc_attr( $value ) . '" class="regular-text" autocomplete="off" />';
    }

    public function render_model_field() {
        $value = get_option( 'noodu_default_model' );
        echo '<input type="text" name="noodu_default_model" value="' . esc_attr( $value ) . '" class="regular-text" placeholder="gpt-4o-mini" />';
    }

    public function render_shortcode( $atts ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return '<p>' . esc_html__( 'You need editing permissions to use the slide generator.', 'noodu-slide-generator' ) . '</p>';
        }

        ob_start();
        include NOODU_PLUGIN_DIR . 'public/shortcode.php';
        return ob_get_clean();
    }
}
