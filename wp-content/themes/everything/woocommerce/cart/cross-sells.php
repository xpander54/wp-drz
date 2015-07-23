<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 * @version    1.6.4
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

global $woocommerce_loop, $woocommerce, $product;

$crosssells = WC()->cart->get_cross_sells();

if (sizeof($crosssells) == 0) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'posts_per_page'      => apply_filters('woocommerce_cross_sells_total', $posts_per_page),
	'no_found_rows'       => 1,
	'orderby'             => 'rand',
	'post__in'            => $crosssells,
	'meta_query'          => $meta_query
);

$products = new WP_Query($args);

$woocommerce_loop['columns'] = apply_filters('woocommerce_cross_sells_columns', $columns);

if ($products->have_posts()): ?>

	<hr class="divider" />

	<h2><?php _e('You may be interested in&hellip;', 'woocommerce'); ?></h2>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ($products->have_posts()): $products->the_post(); ?>

			<?php woocommerce_get_template_part('content', 'product'); ?>

		<?php endwhile; ?>

	<?php woocommerce_product_loop_end(); ?>

<?php endif;

wp_reset_query();