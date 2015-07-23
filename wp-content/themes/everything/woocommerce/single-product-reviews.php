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

global $woocommerce, $product;

if (!comments_open()) {
	return;
}

?>

<h2>
	<?php
		if (get_option('woocommerce_enable_review_rating') == 'yes' && ($count = $product->get_rating_count()) > 0) {
			printf(_n('%s review for %s', '%s reviews for %s', $count, 'woocommerce'), $count, get_the_title());
		} else {
			_e('Reviews', 'woocommerce');
		}
	?>
</h2>

<?php if (have_comments()): ?>

	<ul class="comments">
		<?php
			wp_list_comments(apply_filters('woocommerce_product_review_list_args', array(
				'style'        => 'div',
				'callback'     => 'woocommerce_comments',
				'end-callback' => function() { echo '</ul></li>'; }
			)));
		?>
	</ul>

	<?php echo Everything::getPaginateLinks('comments'); ?>

<?php else: ?>

	<p><?php _e('There are no reviews yet.', 'woocommerce'); ?></p>

<?php endif; ?>

<?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->id)): ?>

	<?php

		$commenter = wp_get_current_commenter();

		$comment_form = array(
			'title_reply'   => have_comments() ? __('Add a review', 'woocommerce') : __('Be the first to review', 'woocommerce').' &ldquo;'.get_the_title().'&rdquo;',
			'fields'        => array(
				'author' => '<li class="col-1-2"><input class="full-width" type="text" name="author" placeholder="'.__('Name', 'woocommerce').'*" value="'.esc_attr($commenter['comment_author']).'" /></li>',
				'email'  => '<li class="col-1-2"><input class="full-width" type="text" name="email" placeholder="'.__('E-mail', 'woocommerce').' ('.__('not published', 'woocommerce').')*" value="'.esc_attr($commenter['comment_author_email']).'" /></li>'
			),
			'label_submit'  => __('Submit', 'woocommerce'),
			'logged_in_as'  => '',
			'comment_field' => '<p><textarea class="full-width" name="comment" placeholder="'.__('Your Review', 'woocommerce').'"></textarea></p>'
		);

		if (get_option('woocommerce_enable_review_rating') == 'yes') {
			$comment_form['comment_field'] .= '<div id="comment-form-rating" class="alignright fixed"><label for="rating">'.__('Your Rating', 'woocommerce').'</label><select name="rating">
				<option value="">'.__('Rate&hellip;', 'woocommerce').'</option>
				<option value="5">'.__('Perfect', 'woocommerce').'</option>
				<option value="4">'.__('Good', 'woocommerce').'</option>
				<option value="3">'.__('Average', 'woocommerce').'</option>
				<option value="2">'.__('Not that bad', 'woocommerce').'</option>
				<option value="1">'.__('Very Poor', 'woocommerce').'</option>
			</select></div>';
		}

		$comment_form['comment_field'] .= wp_nonce_field('woocommerce-comment_rating', '_wpnonce', true, false);

		comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));

	?>

<?php else: ?>

	<p><?php _e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?></p>

<?php endif; ?>