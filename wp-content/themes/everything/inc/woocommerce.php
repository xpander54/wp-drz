<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

function woocommerce_upsell_display($posts_per_page = '-1', $columns = 4, $orderby = 'rand') {
	woocommerce_get_template('single-product/up-sells.php', array(
		'posts_per_page'  => $posts_per_page,
		'orderby'         => apply_filters('woocommerce_upsells_orderby', $orderby),
		'columns'         => $columns
	));
}

// -----------------------------------------------------------------------------

function woocommerce_get_product_thumbnail($size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0)
{

	// Figure
	$figure = \Drone\HTML::figure()
		->class('thumbnail featured full-width');

	// Hyperlink
	$a = $figure->addNew('a')
		->attr(Everything::getImageAttrs('a', array('border' => false, 'hover' => Everything::to('woocommerce/shop/image_hover'), 'fancybox' => false)))
		->href(get_permalink());

	// Image
	if (has_post_thumbnail()) {
		$a->add(get_the_post_thumbnail($GLOBALS['post']->ID, $size));
		if (Everything::to('woocommerce/shop/image_hover') == 'image') {
			$attachment_ids = $GLOBALS['product']->get_gallery_attachment_ids();
			if (isset($attachment_ids[0])) {
				$a->add(wp_get_attachment_image($attachment_ids[0], $size));
			}
		}
	} elseif (woocommerce_placeholder_img_src()) {
		$a->add(woocommerce_placeholder_img($size));
	}

	return $figure->html();

}

// -----------------------------------------------------------------------------

function woocommerce_subcategory_thumbnail($category)
{

	// Figure
	$figure = \Drone\HTML::figure()
		->class('featured full-width');

	// Hyperlink
	$a = $figure->addNew('a')
		->attr(Everything::getImageAttrs('a', array('fancybox' => false)))
		->href(get_term_link($category->slug, 'product_cat'));

	// Thumbnail
	$thumbnail_id   = get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);
	$thumbnail_size = apply_filters('single_product_small_thumbnail_size', 'shop_catalog');

	if ($thumbnail_id) {
		$a->add(wp_get_attachment_image($thumbnail_id, $thumbnail_size));
	} elseif (woocommerce_placeholder_img_src()) {
		$a->add(woocommerce_placeholder_img($thumbnail_size));
	}

	echo $figure->html();

}