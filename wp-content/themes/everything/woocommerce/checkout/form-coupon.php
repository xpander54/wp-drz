<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 * @version    2.2.0
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

if (!WC()->cart->coupons_enabled()) {
	return;
}

$info_message = apply_filters('woocommerce_checkout_coupon_message', __('Have a coupon?', 'woocommerce').' <a href="#" class="showcoupon">'.__('Click here to enter your code', 'woocommerce').'</a>');
wc_print_notice($info_message, 'notice');

?>

<form class="checkout_coupon" method="post" style="display: none;">

	<p class="form-row form-row-first">
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php _e('Coupon code', 'woocommerce'); ?>" id="coupon_code" value="" />
	</p>

	<p class="form-row form-row-last">
		<input type="submit" class="button" name="apply_coupon" value="<?php _e('Apply Coupon', 'woocommerce'); ?>" />
	</p>

	<div class="clear"></div>

	<hr style="margin-top: 8px;" />

</form>