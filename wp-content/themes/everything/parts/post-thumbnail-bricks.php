<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */
?>

<?php if (
	has_post_thumbnail() && !post_password_required() &&
	apply_filters('everything_post_thumbnail_display', Everything::to_(array(sprintf('format_posts/%s/thumbnail', get_post_format()), 'format_posts/standard/thumbnail'))->value(is_singular() ? 'single' : 'list'))
): ?>
	<figure class="thumbnail full-width featured">
		<?php if (is_singular()): ?>
			<div <?php Everything::imageAttrs('div', array('border' => false)); ?>>
				<?php the_post_thumbnail('auto'); ?>
			</div>
		<?php else: ?>
			<a href="<?php the_permalink(); ?>" <?php Everything::imageAttrs('a', array('border' => false)); ?>>
				<?php the_post_thumbnail('auto'); ?>
			</a>
		<?php endif; ?>
	</figure>
<?php endif; ?>