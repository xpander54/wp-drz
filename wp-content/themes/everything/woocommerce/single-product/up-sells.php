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

global $product, $woocommerce, $woocommerce_loop;

$upsells = $product->get_upsells();

if (sizeof($upsells) == 0) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array($product->id),
	'meta_query'          => $meta_query
);

$products = new WP_Query($args);

$woocommerce_loop['columns'] = $columns;

if ($products->have_posts()): ?>

	<hr />

	<h2><?php _e('You may also like&hellip;', 'woocommerce'); ?></h2>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ($products->have_posts()): $products->the_post(); ?>

			<?php woocommerce_get_template_part('content', 'product'); ?>

		<?php endwhile; ?>

	<?php woocommerce_product_loop_end(); ?>

<?php endif;

wp_reset_postdata();