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

global $woocommerce_loop;

// Store loop count we're currently on
if (empty($woocommerce_loop['loop'])) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns'])) {
	$woocommerce_loop['columns'] = apply_filters('loop_shop_columns', Everything::to('woocommerce/shop/columns'));
}

// Increase loop count
$woocommerce_loop['loop']++;

?>

<li class="product-category product col-1-<?php echo $woocommerce_loop['columns']; ?>">

	<?php do_action('woocommerce_before_subcategory', $category); ?>

	<?php do_action('woocommerce_before_subcategory_title', $category); ?>

	<h3>
		<a href="<?php echo get_term_link($category->slug, 'product_cat'); ?>">
			<?php
				echo $category->name;
				if ($category->count > 0) {
					echo apply_filters('woocommerce_subcategory_count_html', ' <small class="count">('.$category->count.')</small>', $category);
				}
			?>
		</a>
	</h3>

	<?php do_action('woocommerce_after_subcategory_title', $category); ?>

	<?php do_action('woocommerce_after_subcategory', $category); ?>

</li>