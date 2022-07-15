<?php
/**
 * All necessary custom functions will be here.
 *
 * @package WCPP
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the gateway to WooCommerce.
 *
 * @param  array $gateways WooCommerce payment methods.
 */
function wcpp_gateway_class( $gateways ) {
	$gateways[] = 'WCPP\WCPP_Gateway';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wcpp_gateway_class' );

/**
 * Add extra profile field to user profile.
 */
function wcpp_add_extra_field_user_balance() {
	?>
	<table class="form-table">
		<tr>
			<th><label for="user_balance"><?php esc_html_e( 'User Balance', 'wcpp' ); ?></label></th>
			<td>
				<input type="number" name="user_balance" id="user_balance" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'user_balance', true ) ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'wcpp_add_extra_field_user_balance' );
add_action( 'edit_user_profile', 'wcpp_add_extra_field_user_balance' );


/**
 * Save extra profile field.
 *
 * @param int $user_id User ID.
 */
function wcpp_save_extra_user_profile_fields( $user_id ) {
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	if ( isset( $_POST['user_balance'] ) ) {
		update_user_meta( $user_id, 'user_balance', sanitize_text_field( $_POST['user_balance'] ) );
	}
}
add_action( 'personal_options_update', 'wcpp_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'wcpp_save_extra_user_profile_fields' );
