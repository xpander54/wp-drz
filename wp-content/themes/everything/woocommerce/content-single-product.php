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

?>

<?php do_action('woocommerce_before_single_product'); ?>

<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="columns">
		<?php $columns = Everything::stringToColumns(Everything::to('woocommerce/product/image_size')); ?>
		<ul>
			<li class="<?php echo $columns[0]['class']; ?>">
				<?php do_action('woocommerce_before_single_product_summary'); ?>
			</li>
			<li class="<?php echo $columns[1]['class']; ?>">
				<div class="summary entry-summary">
					<?php do_action('woocommerce_single_product_summary'); ?>
				</div>
			</li>
		</ul>
	</div>

	<?php do_action('woocommerce_after_single_product_summary'); ?>

</div>

<?php do_action('woocommerce_after_single_product'); ?>