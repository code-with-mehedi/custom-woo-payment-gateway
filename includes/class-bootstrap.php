<?php
/**
 * Bootstrap class.
 *
 * @package WCPP
 */

namespace WCPP;

use WCPP\Traits\Singleton;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load core functionality inside this class.
 */
class Bootstrap {

	use Singleton;

	/**
	 * Constructor of Bootstrap class.
	 */
	private function __construct() {

		// Include custom function files.
		$this->custom_functions();

		// load payment gateway when plugins loaded hooks trigerr.
		add_action( 'plugins_loaded', array( $this, 'wcpp_gateway_loader' ) );
	}

	/**
	 * Load custom functions.
	 */
	private function custom_functions() {
		require_once WCPP_PLUGIN_PATH . 'includes/custom-functions.php';
	}

	/**
	 * Load payment gateway.
	 */
	public function wcpp_gateway_loader() {
		require_once WCPP_PLUGIN_PATH . 'includes/class-wcpp-gateway.php';
	}

}
