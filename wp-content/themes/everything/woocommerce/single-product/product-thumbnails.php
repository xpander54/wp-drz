<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 * @version    2.0.3
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

if (count($attachment_ids) == 0) {
	return;
}

$columns = apply_filters('woocommerce_product_thumbnails_columns', Everything::to('woocommerce/product/thumbnails_columns'));

?>

<div class="columns">
	<ul>
		<?php
			foreach ($attachment_ids as $attachment_id):
				if (!($url = wp_get_attachment_url($attachment_id))) {
					continue;
				}
		?>
			<li class="col-1-<?php echo $columns; ?>">
				<figure class="full-width">
					<a href="<?php echo $url; ?>" data-fancybox-title="<?php echo esc_attr(Everything::woocommerceGetThumbnailCaption($attachment_id)); ?>" data-fancybox-group="product-gallery" <?php Everything::imageAttrs('a'); ?>>
						<?php echo wp_get_attachment_image($attachment_id, apply_filters('single_product_small_thumbnail_size', 'shop_thumbnail')); // missing filter: woocommerce_single_product_image_thumbnail_html ?>
					</a>
				</figure>
			</li>
		<?php endforeach; ?>
	</ul>
</div>