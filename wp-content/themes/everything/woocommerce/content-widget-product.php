<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

global $product;

?>

<li>
	<figure class="alignright fixed">
		<a href="<?php echo esc_url(get_permalink($product->id)); ?>" title="<?php echo esc_attr($product->get_title()); ?>" <?php Everything::imageAttrs('a', array('border' => true, 'hover' => '', 'fanbcybox' => false)); ?>>
			<?php echo $product->get_image('post-thumbnail-mini'); ?>
		</a>
	</figure>
	<h3>
		<a href="<?php echo esc_url(get_permalink($product->id)); ?>" title="<?php echo esc_attr($product->get_title()); ?>">
			<?php echo $product->get_title(); ?>
		</a>
	</h3>
	<p><?php echo $product->get_price_html(); ?></p>
	<?php if (!empty($show_rating) && ($average = $product->get_average_rating()) > 0): ?>
		<?php Everything::shortcodeOutput('rating', array('rate' => $average.'/5', 'advanced_tag' => 'small')); ?>
	<?php endif; ?>
</li>