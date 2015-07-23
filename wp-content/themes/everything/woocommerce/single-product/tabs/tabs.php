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

$tabs = apply_filters('woocommerce_product_tabs', array());

if (!empty($tabs)): ?>

	<div class="woocommerce-tabs tabs">
		<?php foreach ($tabs as $key => $tab): ?>
			<div id="<?php echo $key; ?>" title="<?php echo apply_filters('woocommerce_product_'.$key.'_tab_title', $tab['title'], $key); ?>">
				<?php call_user_func($tab['callback'], $key, $tab); ?>
			</div>
		<?php endforeach; ?>
	</div>

<?php endif; ?>