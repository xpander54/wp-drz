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
		<article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>><?php
			get_template_part('parts/post-thumbnail', Everything::getBlogStyle() == 'bricks' ? 'bricks' : get_post_format());
			Everything::meta('before');
			?>
			<h2 class="entry-title">
			<?php
			Everything::title();
			?>
			</h2>
			<?php
			the_content(null, Everything::to('post/strip_teaser'));
			echo Everything::getPaginateLinks('page');
		?></article>
	</section>
	<span class="vcard author post-author"><span class="fn">
	<?php get_template_part('parts/author-bio'); ?>
	</span></span>
	<?php Everything::socialButtons(); ?>
	<?php Everything::meta(); ?>
	<?php comments_template(); ?>

<?php endif; ?>

<?php get_footer(); ?>
