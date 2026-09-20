<?php

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

        // REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

        // Shortcode
        add_shortcode( 'noodu_generator', array( $this, 'render_shortcode' ) );
    }

    public static function activate() {
        // Create uploads directory
        $upload_dir = wp_upload_dir();
        $noodu_dir = $upload_dir['basedir'] . '/noodu-slides';
        if ( ! is_dir( $noodu_dir ) ) {
            wp_mkdir_p( $noodu_dir );
        }

        // Create database table for projects
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'noodu_projects';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                name varchar(255) NOT NULL,
                code varchar(50) NOT NULL,
                prompt longtext NOT NULL,
                model varchar(100) NOT NULL,
                status varchar(50) DEFAULT 'generating',
                pdf_file varchar(255),
                slide_count int DEFAULT 0,
                slides_data longtext,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY user_id (user_id)
            ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }

        // Create revisions table
        $revisions_table = $wpdb->prefix . 'noodu_revisions';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$revisions_table'" ) !== $revisions_table ) {
            $sql = "CREATE TABLE $revisions_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                project_id mediumint(9) NOT NULL,
                prompt longtext NOT NULL,
                status varchar(50) DEFAULT 'pending',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY project_id (project_id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        update_option( 'noodu_plugin_version', NOODU_PLUGIN_VERSION );
    }

    public static function deactivate() {
        // Cleanup on deactivation if needed
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'Noodu Slide Generator', 'noodu-slide-generator' ),
            __( 'Noodu', 'noodu-slide-generator' ),
            'manage_options',
            'noodu-slide-generator',
            array( $this, 'render_admin_page' ),
            'dashicons-images-alt2',
            25
        );

        add_submenu_page(
            'noodu-slide-generator',
            __( 'Dashboard', 'noodu-slide-generator' ),
            __( 'Dashboard', 'noodu-slide-generator' ),
            'manage_options',
            'noodu-slide-generator',
            array( $this, 'render_admin_page' )
        );

        add_submenu_page(
            'noodu-slide-generator',
            __( 'Projects', 'noodu-slide-generator' ),
            __( 'Projects', 'noodu-slide-generator' ),
            'manage_options',
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
        register_setting( 'noodu-settings', 'noodu_openai_base_url' );
        register_setting( 'noodu-settings', 'noodu_openai_api_key' );
        register_setting( 'noodu-settings', 'noodu_default_model' );

        add_settings_section(
            'noodu-api-settings',
            __( 'OpenAI API Settings', 'noodu-slide-generator' ),
            array( $this, 'render_api_settings_section' ),
            'noodu-settings'
        );

        add_settings_field(
            'noodu_openai_base_url',
            __( 'API Base URL', 'noodu-slide-generator' ),
            array( $this, 'render_base_url_field' ),
            'noodu-settings',
            'noodu-api-settings'
        );

        add_settings_field(
            'noodu_openai_api_key',
            __( 'API Key', 'noodu-slide-generator' ),
            array( $this, 'render_api_key_field' ),
            'noodu-settings',
            'noodu-api-settings'
        );

        add_settings_field(
            'noodu_default_model',
            __( 'Default Model', 'noodu-slide-generator' ),
            array( $this, 'render_model_field' ),
            'noodu-settings',
            'noodu-api-settings'
        );
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'noodu' ) === false ) {
            return;
        }

        wp_enqueue_style( 'noodu-admin', NOODU_PLUGIN_URL . 'assets/admin-style.css', array(), NOODU_PLUGIN_VERSION );
        wp_enqueue_script( 'noodu-admin', NOODU_PLUGIN_URL . 'assets/admin-script.js', array( 'jquery' ), NOODU_PLUGIN_VERSION, true );

        wp_localize_script( 'noodu-admin', 'nooduAjax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'noodu_nonce' )
        ) );
    }

    public function enqueue_public_scripts() {
        wp_enqueue_style( 'noodu-public', NOODU_PLUGIN_URL . 'assets/public-style.css', array(), NOODU_PLUGIN_VERSION );
        wp_enqueue_script( 'noodu-public', NOODU_PLUGIN_URL . 'assets/public-script.js', array( 'jquery' ), NOODU_PLUGIN_VERSION, true );

        wp_localize_script( 'noodu-public', 'nooduPublic', array(
            'restUrl' => rest_url(),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ) );
    }

    public function register_rest_routes() {
        register_rest_route( 'noodu/v1', '/models', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_models' ),
            'permission_callback' => array( $this, 'check_permission' )
        ) );

        register_rest_route( 'noodu/v1', '/generate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'generate_slides' ),
            'permission_callback' => array( $this, 'check_permission' )
        ) );

        register_rest_route( 'noodu/v1', '/projects', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_projects' ),
            'permission_callback' => array( $this, 'check_permission' )
        ) );

        register_rest_route( 'noodu/v1', '/projects/(?P<id>\d+)', array(
            'methods' => array( 'GET', 'DELETE' ),
            'callback' => array( $this, 'handle_project' ),
            'permission_callback' => array( $this, 'check_permission' )
        ) );

        register_rest_route( 'noodu/v1', '/download/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'download_pdf' ),
            'permission_callback' => array( $this, 'check_permission' )
        ) );
    }

    public function check_permission() {
        return current_user_can( 'manage_options' ) || current_user_can( 'edit_posts' );
    }

    public function get_models( $request ) {
        $base_url = get_option( 'noodu_openai_base_url' );
        $api_key = get_option( 'noodu_openai_api_key' );

        if ( ! $base_url || ! $api_key ) {
            return new WP_Error( 'missing_settings', __( 'OpenAI settings not configured', 'noodu-slide-generator' ), array( 'status' => 400 ) );
        }

        $client = new Noodu_OpenAI_Client( $base_url, $api_key );
        try {
            $models = $client->get_models();
            return rest_ensure_response( array( 'success' => true, 'models' => $models ) );
        } catch ( Exception $e ) {
            return new WP_Error( 'api_error', $e->getMessage(), array( 'status' => 500 ) );
        }
    }

    public function generate_slides( $request ) {
        global $wpdb;

        $base_url = get_option( 'noodu_openai_base_url' );
        $api_key = get_option( 'noodu_openai_api_key' );
        $params = $request->get_json_params();

        $model = $params['model'] ?? '';
        $prompt = $params['prompt'] ?? '';
        $project_name = $params['project_name'] ?? 'Generated Module';

        if ( ! $model || ! $prompt ) {
            return new WP_Error( 'missing_params', __( 'Missing required parameters', 'noodu-slide-generator' ), array( 'status' => 400 ) );
        }

        try {
            $client = new Noodu_OpenAI_Client( $base_url, $api_key, $model );
            $content = $client->generate_slide_content( $prompt );

            $project_code = 'proj_' . bin2hex( random_bytes( 4 ) );
            $user_id = get_current_user_id();

            // Insert project
            $wpdb->insert(
                $wpdb->prefix . 'noodu_projects',
                array(
                    'user_id' => $user_id,
                    'name' => $project_name,
                    'code' => $project_code,
                    'prompt' => $prompt,
                    'model' => $model,
                    'status' => 'processing',
                    'slides_data' => wp_json_encode( $content )
                ),
                array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
            );

            $project_id = $wpdb->insert_id;

            // Generate PDF asynchronously
            wp_schedule_single_event( time(), 'noodu_generate_pdf', array( $project_id ) );

            return rest_ensure_response( array(
                'success' => true,
                'project_id' => $project_id,
                'project_name' => $project_name,
                'code' => $project_code
            ) );
        } catch ( Exception $e ) {
            return new WP_Error( 'generation_error', $e->getMessage(), array( 'status' => 500 ) );
        }
    }

    public function get_projects( $request ) {
        global $wpdb;

        $user_id = get_current_user_id();
        $projects = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}noodu_projects WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            )
        );

        return rest_ensure_response( array( 'success' => true, 'projects' => $projects ) );
    }

    public function handle_project( $request ) {
        global $wpdb;

        $project_id = $request['id'];
        $method = $request->get_method();

        if ( $method === 'GET' ) {
            $project = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}noodu_projects WHERE id = %d",
                    $project_id
                )
            );

            if ( ! $project ) {
                return new WP_Error( 'not_found', __( 'Project not found', 'noodu-slide-generator' ), array( 'status' => 404 ) );
            }

            return rest_ensure_response( array( 'success' => true, 'project' => $project ) );
        } elseif ( $method === 'DELETE' ) {
            $wpdb->delete( $wpdb->prefix . 'noodu_projects', array( 'id' => $project_id ), array( '%d' ) );
            return rest_ensure_response( array( 'success' => true, 'message' => __( 'Project deleted', 'noodu-slide-generator' ) ) );
        }
    }

    public function download_pdf( $request ) {
        global $wpdb;

        $project_id = $request['id'];
        $project = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}noodu_projects WHERE id = %d",
                $project_id
            )
        );

        if ( ! $project || ! $project->pdf_file ) {
            return new WP_Error( 'not_found', __( 'PDF not found', 'noodu-slide-generator' ), array( 'status' => 404 ) );
        }

        $upload_dir = wp_upload_dir();
        $pdf_path = $upload_dir['basedir'] . '/noodu-slides/' . $project->pdf_file;

        if ( ! file_exists( $pdf_path ) ) {
            return new WP_Error( 'file_not_found', __( 'PDF file missing', 'noodu-slide-generator' ), array( 'status' => 404 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'download_url' => $upload_dir['baseurl'] . '/noodu-slides/' . $project->pdf_file
        ) );
    }

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
        echo '<p>' . __( 'Configure your OpenAI API settings', 'noodu-slide-generator' ) . '</p>';
    }

    public function render_base_url_field() {
        $value = get_option( 'noodu_openai_base_url', 'https://api.openai.com/v1' );
        echo '<input type="url" name="noodu_openai_base_url" value="' . esc_attr( $value ) . '" style="width: 100%; max-width: 400px;" />';
    }

    public function render_api_key_field() {
        $value = get_option( 'noodu_openai_api_key' );
        echo '<input type="password" name="noodu_openai_api_key" value="' . esc_attr( $value ) . '" style="width: 100%; max-width: 400px;" />';
    }

    public function render_model_field() {
        $value = get_option( 'noodu_default_model', 'gpt-4' );
        echo '<input type="text" name="noodu_default_model" value="' . esc_attr( $value ) . '" style="width: 100%; max-width: 400px;" />';
    }

    public function render_shortcode( $atts ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return '<p>' . __( 'You do not have permission to use this feature', 'noodu-slide-generator' ) . '</p>';
        }

        ob_start();
        include NOODU_PLUGIN_DIR . 'public/shortcode.php';
        return ob_get_clean();
    }
}
?>
