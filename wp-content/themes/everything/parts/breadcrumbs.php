<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

// Separator
$separator = '&rsaquo;'; // is_rtl() ? '&lsaquo;' : '&rsaquo;'

// bbPress
if (Everything::isPluginActive('bbpress') && Everything::to('bbpress/breadcrumbs') && is_bbpress()) {
	$breadcrumbs_html = bbp_get_breadcrumb(array(
		'before'         => '',
		'after'          => '',
		'sep'            => $separator,
		'sep_before'     => '',
		'sep_after'      => '',
		'current_before' => '',
		'current_after'  => ''
	));
}

// WooCommerce
else if (Everything::isPluginActive('woocommerce') && Everything::to('woocommerce/breadcrumbs') && (is_shop() || is_product_taxonomy() || is_product())) { //  || is_cart() || is_checkout() || is_order_received_page() || is_account_page()
	$breadcrumbs_html = \Drone\Func::functionGetOutputBuffer('woocommerce_breadcrumb', array(
		'delimiter'   => $separator,
		'wrap_before' => '',
		'wrap_after'  => ''
	));
}

// http://wordpress.org/extend/plugins/breadcrumb-navxt/
else if (Everything::isPluginActive('breadcrumb-navxt')) {
	$options   = get_option('bcn_options');
	$separator = $options['hseparator'];
	$breadcrumbs_html = bcn_display(true);
}

// http://wordpress.org/extend/plugins/breadcrumb-trail/
else if (Everything::isPluginActive('breadcrumb-trail')) {
	$breadcrumbs_html = breadcrumb_trail(array(
		'separator'   => $separator,
		'show_browse' => false,
		'echo'        => false
	));
}

// http://wordpress.org/plugins/wordpress-seo/
else if (Everything::isPluginActive('wordpress-seo')) {
	$options   = get_option('wpseo_internallinks');
	$separator = $options['breadcrumbs-sep'] ? $options['breadcrumbs-sep'] : '&raquo;';
	$breadcrumbs_html = yoast_breadcrumb('', '', false);
}

else {
	return;
}

// No breadcrumbs
if (!$breadcrumbs_html) {
	return;
}

// Processing breadcrumbs
if ($separator) {
	$breadcrumbs = explode($separator, $breadcrumbs_html);
}
$breadcrumbs = array_map(function($a) { return '<li>'.trim($a).'</li>'; }, (array)$breadcrumbs);

?>

<ul class="breadcrumbs alt"><?php echo implode(' ', $breadcrumbs); ?></ul>