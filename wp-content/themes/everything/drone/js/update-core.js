/**
 * @package    WordPress
 * @subpackage Drone
 * @since      4.1
 */

// -----------------------------------------------------------------------------

(function($) {
	
	'use strict';

	// jQuery
	$(document).ready(function($) {

		// Disable theme update
		$('#update-themes-table tr:has(.check-column input[value="'+drone_update_core.template+'"])').each(function() {
			$('.check-column input', this).prop('disabled', true).prop('checked', false);
			$('.plugin-title', this).append('<br />'+drone_update_core.notice);
		});
		
		// Select all - disable fix
		$('#themes-select-all, #themes-select-all-2').change(function() {
			$('#update-themes-table .check-column input:disabled').prop('checked', false);
		});

	});
	
})(jQuery);