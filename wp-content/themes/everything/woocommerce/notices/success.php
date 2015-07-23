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

if (!$messages) {
	return;
}

foreach ($messages as $message) {
	Everything::shortcodeOutput('message', array('color' => 'green'), '<i class="icon-check"></i> '.wp_kses_post($message));
}