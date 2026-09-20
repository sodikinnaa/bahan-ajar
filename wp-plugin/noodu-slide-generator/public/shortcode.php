<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$default_model = get_option( 'noodu_default_model' );
?>
<div class="noodu-app">
    <h2 class="noodu-app__title"><?php esc_html_e( 'Generate a slide deck', 'noodu-slide-generator' ); ?></h2>

    <div class="noodu-notice" style="display:none;"></div>

    <div class="noodu-field">
        <label for="noodu-project-name"><?php esc_html_e( 'Project name', 'noodu-slide-generator' ); ?></label>
        <input type="text" id="noodu-project-name" placeholder="<?php esc_attr_e( 'Modul 3 — Run Your Project', 'noodu-slide-generator' ); ?>" />
    </div>

    <div class="noodu-field">
        <label for="noodu-model"><?php esc_html_e( 'Model', 'noodu-slide-generator' ); ?></label>
        <select id="noodu-model">
            <?php if ( $default_model ) : ?>
                <option value="<?php echo esc_attr( $default_model ); ?>"><?php echo esc_html( $default_model ); ?></option>
            <?php else : ?>
                <option value=""><?php esc_html_e( 'Select a model…', 'noodu-slide-generator' ); ?></option>
            <?php endif; ?>
        </select>
        <button type="button" class="noodu-btn noodu-btn--ghost" id="noodu-fetch-models">
            <?php esc_html_e( 'Fetch available models', 'noodu-slide-generator' ); ?>
        </button>
    </div>

    <div class="noodu-field">
        <label for="noodu-prompt"><?php esc_html_e( 'Prompt', 'noodu-slide-generator' ); ?></label>
        <textarea id="noodu-prompt" rows="7" placeholder="<?php esc_attr_e( 'Describe the deck you want…', 'noodu-slide-generator' ); ?>"></textarea>
    </div>

    <div class="noodu-field">
        <label for="noodu-reference"><?php esc_html_e( 'Reference file (optional)', 'noodu-slide-generator' ); ?></label>
        <input type="file" id="noodu-reference" accept=".pdf,.png,.jpg,.jpeg" />
    </div>

    <button type="button" class="noodu-btn noodu-btn--primary" id="noodu-generate">
        <?php esc_html_e( 'Generate slides', 'noodu-slide-generator' ); ?>
    </button>

    <div class="noodu-loading" style="display:none;">
        <span class="noodu-spinner"></span>
        <p><?php esc_html_e( 'Writing your deck. This usually takes under a minute.', 'noodu-slide-generator' ); ?></p>
    </div>

    <div class="noodu-projects" style="display:none;">
        <h3><?php esc_html_e( 'Your decks', 'noodu-slide-generator' ); ?></h3>
        <div class="noodu-projects__grid"></div>
    </div>
</div>
