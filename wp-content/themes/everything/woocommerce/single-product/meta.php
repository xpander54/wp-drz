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

if (!Everything::to('woocommerce/product/meta/visible') || count(Everything::to_('woocommerce/product/meta/items')->values()) == 0) {
	return;
}

global $post, $product;

?>

<hr />

<p class="product_meta">

	<?php

		do_action('woocommerce_product_meta_start');

		foreach (Everything::to('woocommerce/product/meta/items') as $item) {
			switch ($item) {
				case 'sku':
					if ($product->is_type(array('simple', 'variable')) && get_option('woocommerce_enable_sku') == 'yes' && $product->get_sku()) {
						printf('<span itemprop="productID" class="sku_wrapper">%s <span class="sku">%s</span>.</span>', __('SKU:', 'woocommerce'), $product->get_sku());
					}
					break;
				case 'categories':
					$size = sizeof(get_the_terms($post->ID, 'product_cat'));
					echo $product->get_categories(', ', '<span class="posted_in">'._n('Category:', 'Categories:', $size, 'woocommerce').' ', '.</span>');
					break;
				case 'tags':
					$size = sizeof(get_the_terms($post->ID, 'product_tag'));
					echo $product->get_tags(', ', '<span class="tagged_as">'._n('Tag:', 'Tags:', $size, 'woocommerce').' ', '.</span>');
					break;
				case 'brands':
					$GLOBALS['WC_Brands']->show_brand();
					break;
			}
		}

		do_action('woocommerce_product_meta_end');

	?>

</p>