<?php
/**
 * Payment gateway class.
 *
 * @package WCPP
 */

namespace WCPP;

use WC_Payment_Gateway;
use WC_Order;

/**
 * Custom Payment gateway class.
 */
class WCPP_Gateway extends WC_Payment_Gateway {

		/**
		 * Class constructor.
		 */
	public function __construct() {

		$this->id                 = 'wcpp';
		$this->icon               = '';
		$this->has_fields         = false;
		$this->method_title       = 'WCPP custom payment gateway';
		$this->method_description = 'Custom payment gateway to place order based on user meta value';

		$this->supports = array(
			'products',
		);

		$this->init_form_fields();

		$this->init_settings();
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

		/**
		 * Plugin options
		 */
	public function init_form_fields() {

			$this->form_fields = array(
				'enabled'     => array(
					'title'       => 'Enable/Disable',
					'label'       => 'Enable WCCP Gateway',
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no',
				),
				'title'       => array(
					'title'       => 'Title',
					'type'        => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'     => 'WCPP Custom Payment Gateway',
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => 'Custom payment gateway to place order based on user meta value',
					'default'     => 'Custom payment gateway to place order based on user meta value',
				),
			);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {

		// First check if user is logged in.
		if ( ! is_user_logged_in() ) {
			wc_add_notice( 'You must be logged in to place order', 'error' );
			return;
		}

		global $woocommerce;
		$order = new WC_Order( $order_id );

		$user_id      = get_current_user_id();
		$user_balance = get_user_meta( $user_id, 'user_balance', true );
		$order_total  = $order->get_total();
		$order_total  = floatval( $order_total );
		$user_balance = floatval( $user_balance );

		// Check if user has enough balance to pay for order.
		if ( $user_balance < $order_total ) {
			wc_add_notice( 'You don\'t have enough balance to place order', 'error' );
			return;
		}
		$user_balance = $user_balance - $order_total;
		update_user_meta( $user_id, 'user_balance', $user_balance );

		$order->payment_complete();
		$woocommerce->cart->empty_cart();
		wc_reduce_stock_levels( $order_id );

		// Add order note to customer.
		$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

		// After succcessful payment redirect to thank you page.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
