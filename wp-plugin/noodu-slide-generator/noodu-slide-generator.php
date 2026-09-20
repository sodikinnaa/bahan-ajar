<?php
/**
 * Plugin Name: Noodu Slide Generator
 * Plugin URI: https://noodu.academy
 * Description: AI-powered educational slide generator integrated with OpenAI API
 * Version: 1.0.0
 * Author: Noodu Academy
 * Author URI: https://noodu.academy
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: noodu-slide-generator
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'NOODU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NOODU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NOODU_PLUGIN_VERSION', '1.0.0' );

// Include required files
require_once NOODU_PLUGIN_DIR . 'includes/class-noodu-plugin.php';
require_once NOODU_PLUGIN_DIR . 'includes/class-database.php';
require_once NOODU_PLUGIN_DIR . 'includes/class-openai-client.php';
require_once NOODU_PLUGIN_DIR . 'includes/class-file-uploader.php';
require_once NOODU_PLUGIN_DIR . 'includes/class-pdf-generator.php';

// Activation and deactivation hooks
register_activation_hook( __FILE__, array( 'Noodu_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Noodu_Plugin', 'deactivate' ) );

// Initialize the plugin
add_action( 'plugins_loaded', array( 'Noodu_Plugin', 'get_instance' ) );

// Load plugin text domain
add_action( 'init', function() {
    load_plugin_textdomain( 'noodu-slide-generator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );
?>
