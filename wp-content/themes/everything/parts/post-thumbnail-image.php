<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */
?>

<?php if (
	has_post_thumbnail() && !post_password_required() &&
	apply_filters('everything_post_thumbnail_display', Everything::to_('format_posts/image/thumbnail')->value(is_singular() ? 'single' : 'list'))
): ?>
	<figure class="thumbnail full-width">
		<a href="<?php
			if (Everything::to('format_posts/image/link') == 'post' && !is_singular()) {
				the_permalink();
			} else {
				list($src) = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
				echo $src;
			}
		?>" <?php Everything::imageAttrs('a'); ?>>
			<?php the_post_thumbnail('full-width'); ?>
		</a>
	</figure>
<?php endif; ?>