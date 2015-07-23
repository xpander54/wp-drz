<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */
?>

<?php get_header(); ?>

<?php if (have_posts()) : ?>

	<section class="section">
		<div class="bricks" data-bricks-columns="<?php echo Everything::to('portfolio/archive/columns'); ?>">
			<?php while (have_posts()): the_post(); ?>
				<div>
					<article id="portfolio-item-<?php the_ID(); ?>" <?php post_class(array('portfolio-item', 'bordered')); // todo: bordered - zalezne od ustawien ?>>
						<?php if (has_post_thumbnail()): ?>
							<figure class="thumbnail featured full-width">
								<a href="<?php the_permalink(); ?>" <?php Everything::imageAttrs('a', array('border' => false, 'fancybox' => false)); ?>>
									<?php the_post_thumbnail('auto'); ?>
								</a>
							</figure>
						<?php endif; ?>
						<?php if (Everything::to('portfolio/archive/title')): ?>
							<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						<?php endif; ?>
						<?php
							switch (Everything::to('portfolio/archive/content/content', '__hidden')) {
								case 'excerpt':
									the_excerpt();
									break;
								case 'excerpt_content':
									if (has_excerpt()) {
										the_excerpt();
										break;
									}
								case 'content':
									the_content(Everything::getReadMore());
									break;
							}
						?>
						<?php if (Everything::to('portfolio/archive/taxonomy/visible')): ?>
							<?php the_terms(get_the_ID(), 'portfolio-'.Everything::to('portfolio/archive/taxonomy/taxonomy'), '<p class="small alt">', ', ', '</p>'); ?>
						<?php endif; ?>
					</article>
				</div>
			<?php endwhile; ?>
		</div>
		<?php echo Everything::getPaginateLinks('portfolio'); ?>
	</section>

<?php else: ?>

	<?php get_template_part('parts/no-posts'); ?>

<?php endif; ?>

<?php get_footer(); ?>