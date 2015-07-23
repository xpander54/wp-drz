<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 * @version    2.1.0
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

global $woocommerce;
?>

<?php do_action('woocommerce_before_mini_cart'); ?>

<ul class="cart_list product_list_widget posts-list <?php echo $args['list_class']; ?>">

	<?php if (sizeof(WC()->cart->get_cart()) > 0): ?>

		<?php
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item):

				$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

				if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)):

					$product_name  = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
					$thumbnail     = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('post-thumbnail-mini'), $cart_item, $cart_item_key);
					$product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);

		?>

			<li>
				<figure class="alignright fixed">
					<a href="<?php echo get_permalink($product_id); ?>" <?php Everything::imageAttrs('a', array('border' => true, 'hover' => '', 'fanbcybox' => false)); ?>>
						<?php echo $thumbnail; ?>
					</a>
				</figure>
				<h3>
					<a href="<?php echo get_permalink($product_id); ?>">
						<?php echo $product_name; ?>
					</a>
				</h3>
				<div>
					<?php echo WC()->cart->get_item_data($cart_item); ?>
					<?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">'.sprintf('%s &times; %s', $cart_item['quantity'], $product_price).'</span>', $cart_item, $cart_item_key); ?>
				</div>
			</li>

		<?php
				endif;
			endforeach;
		?>

	<?php else: ?>

		<li class="empty"><?php _e('No products in the cart.', 'woocommerce'); ?></li>

	<?php endif; ?>

</ul>

<?php if (sizeof(WC()->cart->get_cart()) > 0): ?>

	<p class="total"><?php _e('Subtotal', 'woocommerce'); ?>: <?php echo WC()->cart->get_cart_subtotal(); ?></p>

	<?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>

	<p class="buttons">
		<a href="<?php echo WC()->cart->get_cart_url(); ?>" class="button small"><span><?php _e('View Cart', 'woocommerce'); ?></span></a>
		<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="button small checkout" style="border-color: #129a00; color: #129a00;"><span><?php _e('Checkout', 'woocommerce'); ?></span><i class="icon-right-bold"></i></a>
	</p>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>