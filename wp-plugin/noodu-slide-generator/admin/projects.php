<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$projects = Noodu_Database::get_projects( current_user_can( 'manage_options' ) ? null : get_current_user_id() );
?>
<div class="wrap noodu-wrap">
    <h1><?php esc_html_e( 'Projects', 'noodu-slide-generator' ); ?></h1>

    <div id="noodu-notice" class="notice" style="display:none;"><p></p></div>

    <?php if ( empty( $projects ) ) : ?>
        <div class="notice notice-info">
            <p>
                <?php esc_html_e( 'No decks yet.', 'noodu-slide-generator' ); ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=noodu-slide-generator' ) ); ?>"><?php esc_html_e( 'Generate your first one', 'noodu-slide-generator' ); ?></a>
            </p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Name', 'noodu-slide-generator' ); ?></th>
                    <th><?php esc_html_e( 'Model', 'noodu-slide-generator' ); ?></th>
                    <th><?php esc_html_e( 'Slides', 'noodu-slide-generator' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'noodu-slide-generator' ); ?></th>
                    <th><?php esc_html_e( 'Created', 'noodu-slide-generator' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'noodu-slide-generator' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $projects as $project ) : ?>
                    <?php
                    $ext      = strtolower( pathinfo( $project->pdf_file, PATHINFO_EXTENSION ) );
                    $file_url = $project->pdf_file ? Noodu_Database::file_url( $project->pdf_file ) : '';
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $project->name ); ?></strong></td>
                        <td><?php echo esc_html( $project->model ); ?></td>
                        <td><?php echo (int) $project->slide_count; ?></td>
                        <td>
                            <span class="noodu-status noodu-status--<?php echo esc_attr( $project->status ); ?>">
                                <?php echo esc_html( $project->status ); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( mysql2date( get_option( 'date_format' ), $project->created_at ) ); ?></td>
                        <td>
                            <?php if ( $file_url ) : ?>
                                <a href="<?php echo esc_url( $file_url ); ?>" class="button button-small" target="_blank" rel="noopener">
                                    <?php esc_html_e( 'Open', 'noodu-slide-generator' ); ?>
                                </a>
                                <a href="<?php echo esc_url( $file_url ); ?>" class="button button-small" download>
                                    <?php echo 'pdf' === $ext ? esc_html__( 'Download PDF', 'noodu-slide-generator' ) : esc_html__( 'Download HTML', 'noodu-slide-generator' ); ?>
                                </a>
                            <?php endif; ?>
                            <button type="button" class="button button-small noodu-delete" data-id="<?php echo (int) $project->id; ?>">
                                <?php esc_html_e( 'Delete', 'noodu-slide-generator' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="description" style="margin-top:14px;">
            <?php esc_html_e( 'HTML decks print to PDF from the browser: Print → Save as PDF, paper size 13.333 × 7.5 in, margins none, background graphics on.', 'noodu-slide-generator' ); ?>
        </p>
    <?php endif; ?>
</div>
