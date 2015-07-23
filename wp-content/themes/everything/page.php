<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */
?>

<?php get_header(); ?>

<?php if (have_posts()): the_post(); ?>

	<section class="section">
		<article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
		
<?php get_template_part('parts/post-thumbnail'); ?>
			<?php Everything::title(); ?>
			<?php the_content(); ?>
			<?php echo Everything::getPaginateLinks('page'); ?>
		</article>
	</section>

	<?php get_template_part('parts/author-bio'); ?>
	<?php Everything::socialButtons(); ?>
	<?php Everything::meta(); ?>
	<?php comments_template(); ?>

<?php endif; ?>

<?php get_footer(); ?>