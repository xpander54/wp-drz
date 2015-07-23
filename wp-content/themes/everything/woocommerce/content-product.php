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

// Store loop count we're currently on
if (empty($woocommerce_loop['loop'])) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns'])) {
	$woocommerce_loop['columns'] = apply_filters('loop_shop_columns', Everything::to('woocommerce/shop/columns'));
}

// Ensure visibility
if (!$product || !$product->is_visible()) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

?>

<li <?php post_class('col-1-'.$woocommerce_loop['columns']); ?>>

	<article class="product">

		<?php do_action('woocommerce_before_shop_loop_item'); ?>

		<?php do_action('woocommerce_before_shop_loop_item_title'); ?>

		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<?php do_action('woocommerce_after_shop_loop_item_title'); ?>

		<?php do_action('woocommerce_after_shop_loop_item'); ?>

	</article>

</li>