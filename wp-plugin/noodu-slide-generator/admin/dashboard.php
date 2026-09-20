<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$configured    = get_option( 'noodu_openai_base_url' ) && get_option( 'noodu_openai_api_key' );
$default_model = get_option( 'noodu_default_model' );
$project_count = count( Noodu_Database::get_projects( current_user_can( 'manage_options' ) ? null : get_current_user_id() ) );
?>
<div class="wrap noodu-wrap">
    <h1><?php esc_html_e( 'Noodu Slide Generator', 'noodu-slide-generator' ); ?></h1>

    <?php if ( ! $configured ) : ?>
        <div class="notice notice-warning">
            <p>
                <?php esc_html_e( 'The API is not configured yet.', 'noodu-slide-generator' ); ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=noodu-settings' ) ); ?>"><?php esc_html_e( 'Open Settings', 'noodu-slide-generator' ); ?></a>
            </p>
        </div>
    <?php endif; ?>

    <div id="noodu-notice" class="notice" style="display:none;"><p></p></div>

    <div class="noodu-container">
        <div class="noodu-card">
            <h2><?php esc_html_e( 'Generate a new deck', 'noodu-slide-generator' ); ?></h2>

            <div class="noodu-form-group">
                <label for="noodu-project-name"><?php esc_html_e( 'Project name', 'noodu-slide-generator' ); ?></label>
                <input type="text" id="noodu-project-name" placeholder="<?php esc_attr_e( 'Modul 3 — Run Your Project', 'noodu-slide-generator' ); ?>" />
            </div>

            <div class="noodu-form-group">
                <label for="noodu-model"><?php esc_html_e( 'Model', 'noodu-slide-generator' ); ?></label>
                <select id="noodu-model">
                    <?php if ( $default_model ) : ?>
                        <option value="<?php echo esc_attr( $default_model ); ?>"><?php echo esc_html( $default_model ); ?></option>
                    <?php else : ?>
                        <option value=""><?php esc_html_e( 'Select a model…', 'noodu-slide-generator' ); ?></option>
                    <?php endif; ?>
                </select>
                <button type="button" class="button" id="noodu-fetch-models"><?php esc_html_e( 'Fetch available models', 'noodu-slide-generator' ); ?></button>
            </div>

            <div class="noodu-form-group">
                <label for="noodu-prompt"><?php esc_html_e( 'Prompt', 'noodu-slide-generator' ); ?></label>
                <textarea id="noodu-prompt" rows="8" placeholder="<?php esc_attr_e( 'Buat 15 slide tentang menjalankan project Node.js di VPS, untuk peserta pemula total…', 'noodu-slide-generator' ); ?>"></textarea>
            </div>

            <div class="noodu-form-group">
                <label for="noodu-reference"><?php esc_html_e( 'Reference file (optional)', 'noodu-slide-generator' ); ?></label>
                <input type="file" id="noodu-reference" accept=".pdf,.png,.jpg,.jpeg" />
                <p class="description"><?php esc_html_e( 'Text is extracted from a PDF and passed to the model as reference material.', 'noodu-slide-generator' ); ?></p>
            </div>

            <button type="button" class="button button-primary button-hero" id="noodu-generate"><?php esc_html_e( 'Generate slides', 'noodu-slide-generator' ); ?></button>

            <div id="noodu-result" class="noodu-result" style="display:none;"></div>
        </div>

        <div class="noodu-side">
            <div class="noodu-card">
                <h3><?php esc_html_e( 'Status', 'noodu-slide-generator' ); ?></h3>
                <p>
                    <strong><?php esc_html_e( 'Projects:', 'noodu-slide-generator' ); ?></strong>
                    <?php echo (int) $project_count; ?>
                </p>
                <p>
                    <strong><?php esc_html_e( 'API:', 'noodu-slide-generator' ); ?></strong>
                    <?php if ( $configured ) : ?>
                        <span class="noodu-ok"><?php esc_html_e( 'Configured', 'noodu-slide-generator' ); ?></span>
                    <?php else : ?>
                        <span class="noodu-bad"><?php esc_html_e( 'Not configured', 'noodu-slide-generator' ); ?></span>
                    <?php endif; ?>
                </p>
                <p>
                    <strong><?php esc_html_e( 'PDF rendering:', 'noodu-slide-generator' ); ?></strong>
                    <?php if ( Noodu_Plugin::check_browser() ) : ?>
                        <span class="noodu-ok"><?php esc_html_e( 'Available', 'noodu-slide-generator' ); ?></span>
                    <?php else : ?>
                        <span class="noodu-bad"><?php esc_html_e( 'HTML only', 'noodu-slide-generator' ); ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="noodu-card">
                <h3><?php esc_html_e( 'Shortcode', 'noodu-slide-generator' ); ?></h3>
                <p><?php esc_html_e( 'Put the generator on any page or post:', 'noodu-slide-generator' ); ?></p>
                <code>[noodu_generator]</code>
            </div>
        </div>
    </div>
</div>
