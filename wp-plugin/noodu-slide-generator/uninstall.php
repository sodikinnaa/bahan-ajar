<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}noodu_revisions" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}noodu_projects" );

delete_option( 'noodu_openai_base_url' );
delete_option( 'noodu_openai_api_key' );
delete_option( 'noodu_default_model' );
delete_option( 'noodu_plugin_version' );

$upload_dir = wp_upload_dir();
$noodu_dir  = trailingslashit( $upload_dir['basedir'] ) . 'noodu-slides';

if ( is_dir( $noodu_dir ) ) {
    foreach ( (array) glob( $noodu_dir . '/*' ) as $file ) {
        if ( is_file( $file ) ) {
            @unlink( $file );
        }
    }
    @rmdir( $noodu_dir );
}
