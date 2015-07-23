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

$html = '';
foreach ($messages as $message) {
	$html .= '<i class="icon-cancel"></i> '.wp_kses_post($message).'<br />';
}
Everything::shortcodeOutput('message', array('color' => 'orange'), $html);