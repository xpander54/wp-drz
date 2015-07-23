<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

get_header();
get_template_part('parts/blog', Everything::to('site/blog/style'));
get_footer();