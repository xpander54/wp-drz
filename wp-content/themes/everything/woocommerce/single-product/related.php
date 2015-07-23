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

global $product, $woocommerce_loop;

$related = $product->get_related($posts_per_page);

if (sizeof($related) == 0) {
	return;
}

$args = apply_filters('woocommerce_related_products_args', array(
	'post_type'	          => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $related,
	'post__not_in'        => array($product->id)
) );

$products = new WP_Query($args);

$woocommerce_loop['columns'] = $columns;

if ($products->have_posts()): ?>

	<hr class="divider" />

	<h2><?php _e('Related Products', 'woocommerce'); ?></h2>

	<?php woocommerce_product_loop_start(); ?>

		<?php while ($products->have_posts()) : $products->the_post(); ?>

			<?php woocommerce_get_template_part('content', 'product'); ?>

		<?php endwhile; ?>

	<?php woocommerce_product_loop_end(); ?>

<?php endif;

wp_reset_postdata();