<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap noodu-settings">
    <h1><?php _e( 'Noodu Settings', 'noodu-slide-generator' ); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields( 'noodu-settings' ); ?>
        <?php do_settings_sections( 'noodu-settings' ); ?>
        <?php submit_button(); ?>
    </form>

    <div class="notice notice-info" style="margin-top: 20px;">
        <p>
            <strong><?php _e( 'Setup Instructions:', 'noodu-slide-generator' ); ?></strong><br/>
            1. <?php _e( 'Get your OpenAI API key from', 'noodu-slide-generator' ); ?> <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com</a><br/>
            2. <?php _e( 'Enter the Base URL (default is https://api.openai.com/v1)', 'noodu-slide-generator' ); ?><br/>
            3. <?php _e( 'Paste your API key', 'noodu-slide-generator' ); ?><br/>
            4. <?php _e( 'Select a default model', 'noodu-slide-generator' ); ?>
        </p>
    </div>

    <div class="notice notice-warning" style="margin-top: 20px;">
        <p>
            <strong><?php _e( 'System Requirements:', 'noodu-slide-generator' ); ?></strong><br/>
            - poppler-utils: <?php echo $this->check_poppler() ? '<span style="color: green;">✓ Installed</span>' : '<span style="color: red;">✗ Not found</span>'; ?><br/>
            - Chromium Browser: <?php echo $this->check_chromium() ? '<span style="color: green;">✓ Found</span>' : '<span style="color: red;">✗ Not found</span>'; ?>
        </p>
    </div>
</div>

<style>
.noonu-settings { padding: 20px; }
.noonu-settings form { max-width: 600px; }
.noonu-settings input[type="text"],
.noonu-settings input[type="password"] {
    width: 100%;
    max-width: 400px;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>
