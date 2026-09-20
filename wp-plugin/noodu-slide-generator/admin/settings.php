<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$has_browser = Noodu_Plugin::check_browser();
$has_poppler = Noodu_Plugin::check_poppler();
?>
<div class="wrap noodu-wrap">
    <h1><?php esc_html_e( 'Noodu Settings', 'noodu-slide-generator' ); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'noodu-settings' );
        do_settings_sections( 'noodu-settings' );
        submit_button();
        ?>
    </form>

    <h2><?php esc_html_e( 'Server capabilities', 'noodu-slide-generator' ); ?></h2>
    <table class="widefat striped" style="max-width:640px;">
        <tbody>
            <tr>
                <td><strong><?php esc_html_e( 'Headless Chromium', 'noodu-slide-generator' ); ?></strong><br/>
                    <span class="description"><?php esc_html_e( 'Renders the deck straight to PDF.', 'noodu-slide-generator' ); ?></span></td>
                <td>
                    <?php if ( $has_browser ) : ?>
                        <span class="noodu-ok"><?php esc_html_e( 'Available', 'noodu-slide-generator' ); ?></span>
                    <?php else : ?>
                        <span class="noodu-bad"><?php esc_html_e( 'Not available', 'noodu-slide-generator' ); ?></span><br/>
                        <span class="description"><?php esc_html_e( 'Decks are delivered as HTML instead. Open one and use Print → Save as PDF at 13.333 × 7.5 in, zero margins.', 'noodu-slide-generator' ); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e( 'pdftotext (poppler-utils)', 'noodu-slide-generator' ); ?></strong><br/>
                    <span class="description"><?php esc_html_e( 'Reads text out of an uploaded PDF reference.', 'noodu-slide-generator' ); ?></span></td>
                <td>
                    <?php if ( $has_poppler ) : ?>
                        <span class="noodu-ok"><?php esc_html_e( 'Available', 'noodu-slide-generator' ); ?></span>
                    <?php else : ?>
                        <span class="noodu-bad"><?php esc_html_e( 'Not available', 'noodu-slide-generator' ); ?></span><br/>
                        <span class="description"><?php esc_html_e( 'Paste reference text into the prompt instead.', 'noodu-slide-generator' ); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="description" style="margin-top:12px;max-width:640px;">
        <?php esc_html_e( 'Neither is required. The plugin works on plain shared hosting; these only decide whether you get a PDF directly and whether PDF references can be read.', 'noodu-slide-generator' ); ?>
    </p>
</div>
