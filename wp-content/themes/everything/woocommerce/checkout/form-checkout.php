<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 * @version    2.0.0
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

global $woocommerce;

wc_print_notices();

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->enable_signup && !$checkout->enable_guest_checkout && !is_user_logged_in()) {
	echo apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce'));
	return;
}

$get_checkout_url = apply_filters('woocommerce_get_checkout_url', WC()->cart->get_checkout_url()); ?>

<form name="checkout" method="post" class="checkout" action="<?php echo esc_url($get_checkout_url); ?>">

	<?php if (sizeof($checkout->checkout_fields) > 0): ?>

		<?php do_action('woocommerce_checkout_before_customer_details'); ?>

		<div class="columns" id="customer_details">
			<ul>

				<li class="col-1-2">
					<div class="col-1">
						<?php do_action('woocommerce_checkout_billing'); ?>
					</div>
				</li>

				<li class="col-1-2">
					<div class="col-2">
						<?php do_action('woocommerce_checkout_shipping'); ?>
					</div>
				</li>

			</ul>
		</div>

		<?php do_action('woocommerce_checkout_after_customer_details'); ?>

		<hr />

		<h3 id="order_review_heading"><?php _e('Your order', 'woocommerce'); ?></h3>

	<?php endif; ?>

	<?php do_action('woocommerce_checkout_order_review'); ?>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>