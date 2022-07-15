<?php
/**
 * Plugin Name: Custom Payment Processor
 * Description: A test payment processor plugin for woocommerce.
 * Version:     1.0.0
 * Author:      Mehedi Hasan
 * Author URI:  https://codewithmehedi.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wcpp
 *
 * @package WCPP
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin url.
if ( ! defined( 'WCPP_PLUGIN_URL' ) ) {
	define( 'WCPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Define plugin path.
if ( ! defined( 'WCPP_PLUGIN_PATH' ) ) {
	define( 'WCPP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}


/**
 * Check if WooCommerce is active
 */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Custom payment processor require', 'wcpp' ) . ' <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> ' . esc_html__( 'here.', 'wccfm' ) . '</p></div>';
		}
	);
	return;

}

/**
 * Include necessary files to initial load of the plugin.
 */
if ( ! class_exists( 'WCPP\Bootstrap' ) ) {
	require_once __DIR__ . '/includes/traits/trait-singleton.php';
	require_once __DIR__ . '/includes/class-bootstrap.php';
}

/**
 * Initialize the plugin functionality.
 *
 * @since  1.0.0
 * @return WCPP\Bootstrap
 */
function wcpp_run() {
	return WCPP\Bootstrap::instance();
}

// Call initialization function.
wcpp_run();
