<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$user_id = get_current_user_id();
$projects = Noodu_Database::get_projects( $user_id );
?>
<div class="wrap noodu-projects">
    <h1><?php _e( 'Projects', 'noodu-slide-generator' ); ?></h1>

    <?php if ( empty( $projects ) ) : ?>
        <div class="notice notice-info">
            <p><?php _e( 'No projects yet. Create your first module in the Dashboard.', 'noodu-slide-generator' ); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php _e( 'Name', 'noodu-slide-generator' ); ?></th>
                    <th><?php _e( 'Model', 'noodu-slide-generator' ); ?></th>
                    <th><?php _e( 'Slides', 'noodu-slide-generator' ); ?></th>
                    <th><?php _e( 'Status', 'noodu-slide-generator' ); ?></th>
                    <th><?php _e( 'Created', 'noodu-slide-generator' ); ?></th>
                    <th><?php _e( 'Actions', 'noodu-slide-generator' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $projects as $project ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $project->name ); ?></strong></td>
                        <td><?php echo esc_html( $project->model ); ?></td>
                        <td><?php echo intval( $project->slide_count ); ?></td>
                        <td>
                            <span class="badge" style="background: #<?php echo ( $project->status === 'completed' ) ? '2c3e50' : 'f39c12'; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <?php echo esc_html( $project->status ); ?>
                            </span>
                        </td>
                        <td><?php echo mysql2date( 'M d, Y', $project->created_at ); ?></td>
                        <td>
                            <a href="admin.php?page=noodu-projects&action=view&id=<?php echo intval( $project->id ); ?>" class="button button-small"><?php _e( 'View', 'noodu-slide-generator' ); ?></a>
                            <?php if ( $project->pdf_file ) : ?>
                                <a href="<?php echo esc_url( wp_upload_dir()['baseurl'] . '/noodu-slides/' . $project->pdf_file ); ?>" class="button button-small" download><?php _e( 'Download', 'noodu-slide-generator' ); ?></a>
                            <?php endif; ?>
                            <button class="button button-small" onclick="deleteProject(<?php echo intval( $project->id ); ?>)"><?php _e( 'Delete', 'noodu-slide-generator' ); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.noodu-projects { padding: 20px; }
.badge { display: inline-block; }
</style>

<script>
function deleteProject( projectId ) {
    if ( !confirm( '<?php _e( 'Are you sure you want to delete this project?', 'noodu-slide-generator' ); ?>' ) ) {
        return;
    }

    fetch( '<?php echo esc_url( rest_url( 'noodu/v1/projects/' ) ); ?>' + projectId, {
        method: 'DELETE',
        headers: {
            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
        }
    } )
    .then( r => r.json() )
    .then( data => {
        if ( data.success ) {
            location.reload();
        } else {
            alert( 'Error: ' + data.message );
        }
    } )
    .catch( err => alert( 'Error: ' + err.message ) );
}
</script>
