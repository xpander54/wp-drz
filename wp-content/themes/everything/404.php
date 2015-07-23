<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */
?>

<?php get_header(); ?>

<section class="section">
	<?php echo \Drone\Func::wpProcessContent(Everything::to('not_found/content')); ?>
</section>

<?php get_footer(); ?>