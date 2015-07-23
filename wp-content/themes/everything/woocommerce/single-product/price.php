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

global $post, $product;

?>

<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<h2 itemprop="price" class="price"><?php echo $product->get_price_html(); ?></h2>

	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>