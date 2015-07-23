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

if (is_user_logged_in() || get_option('woocommerce_enable_checkout_login_reminder') == 'no') {
	return;
}

$info_message  = apply_filters('woocommerce_checkout_login_message', __('Returning customer?', 'woocommerce'));
$info_message .= ' <a href="#" class="showlogin">'.__('Click here to login', 'woocommerce').'</a>';
wc_print_notice($info_message, 'notice');

woocommerce_login_form(array(
	'message'  => __('If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'woocommerce'),
	'redirect' => get_permalink(woocommerce_get_page_id('checkout')),
	'hidden'   => true
));