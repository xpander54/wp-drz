/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

// -----------------------------------------------------------------------------

jQuery(document).ready(function($) {

	// Star ratings for comments
	$('#comment-form-rating select').hide().before('<span class="rating"><i class="icon-rating pad"></i><i class="icon-rating pad"></i><i class="icon-rating pad"></i><i class="icon-rating pad"></i><i class="icon-rating pad"></i></span>');

	$('body')
		.on('click', '#comment-form-rating .rating i', function() {
			var star   = $(this);
			var index  = $(this).index();
			var rating = $(this).closest('#comment-form-rating').find('select');

			rating.val(index+1);
			star.parent().children()
				.removeClass('pad')
				.filter(function(i) { return i > index; }).addClass('pad');

			return false;
		})
		.on('click', '#respond #submit', function() {
			var $rating = $(this).closest('form').find('#comment-form-rating select');
			var rating  = $rating.val();

			if ($rating.size() > 0 && !rating && woocommerce_params.review_rating_required == 'yes') {
				alert(woocommerce_params.i18n_required_rating_text);
				return false;
			}
		});

	// prevent double form submission
	$('form.cart').submit(function() {
		$(this).find(':submit').attr('disabled', 'disabled');
	});

});