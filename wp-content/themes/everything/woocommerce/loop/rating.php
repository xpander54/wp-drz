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

global $product;

if (get_option('woocommerce_enable_review_rating') == 'no') {
	return;
}

?>

<?php if ($average = $product->get_average_rating()): ?>
	<small class="rating" title="<?php printf(__('Rated %s out of 5', 'woocommerce'), $average); ?>">
		<?php Everything::shortcodeOutput('rating', array('rate' => $average.'/5', 'advanced_tag' => '')); ?>
	</small>
<?php else: ?>
	<small class="rating">
		<?php echo str_replace('icon-rating-empty', 'icon-rating-empty pad', Everything::getShortcodeOutput('rating', array('rate' => '0/5', 'advanced_tag' => ''))); ?>
	</small>
<?php endif; ?>