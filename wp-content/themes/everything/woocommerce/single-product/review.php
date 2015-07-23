<?php
/**
 * @package    WooCommerce/Templates
 * @subpackage Everything
 * @since      1.0
 * @version    2.1.0
 */

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

global $post;

$rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));

?>

<li id="comment-<?php comment_ID(); ?>" itemprop="reviews" itemscope itemtype="http://schema.org/Review">
	<section class="comment">
		<?php if (!$GLOBALS['comment']->comment_type): ?>
			<figure class="alignleft fixed inset-border">
				<?php echo get_avatar($comment, apply_filters('woocommerce_review_gravatar_size', 50), '', get_comment_author()); ?>
			</figure>
		<?php endif; ?>
		<p class="info">
			<?php if ($rating && get_option('woocommerce_enable_review_rating') == 'yes'): ?>
				<?php Everything::shortcodeOutput('rating', array('rate' => $rating.'/5', 'advanced_tag' => 'small')); ?>
			<?php endif; ?>
			<?php if (get_option('woocommerce_review_rating_verification_label') == 'yes' && wc_customer_bought_product($comment->comment_author_email, $comment->user_id, $comment->comment_post_ID)): ?>
				<small>
					<?php _e('verified owner', 'woocommerce'); ?>
				</small>
			<?php endif; ?>
			<strong><?php comment_author(); ?></strong>
			<?php if (Everything::to('site/comments/date_format')): ?>
				, <time class="small" datetime="<?php printf('%sT%sZ', get_comment_date('Y-m-d'), get_comment_time('H:i')); ?>" itemprop="datePublished">
					<?php
						switch (Everything::to('site/comments/date_format')) {
							case 'relative': printf(__('%s ago', 'everything'), human_time_diff(get_comment_time('U', true))); break;
							case 'absolute': printf(__('%1$s at %2$s', 'everything'), get_comment_date(), get_comment_time()); break;
						}
					?>
				</time>
			<?php endif; ?>
		</p>
		<article class="text" itemprop="description">
			<?php if ($comment->comment_approved == '0') : ?>
				<p><em><?php _e('Your comment is awaiting approval', 'woocommerce'); ?></em></p>
			<?php endif; ?>
			<?php comment_text(); ?>
		</article>
	</section>
	<ul class="comments">
