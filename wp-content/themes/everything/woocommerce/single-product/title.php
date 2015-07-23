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

if (Everything::$headline_used) {
	return;
}

?>

<h1 itemprop="name"><?php the_title(); ?></h1>