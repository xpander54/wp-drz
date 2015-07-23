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

Everything::shortcodeOutput('message', array(), '<i class="icon-info-circled"></i> '.__('No products found which match your selection.', 'woocommerce'));