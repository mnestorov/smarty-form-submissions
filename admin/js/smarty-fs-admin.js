(function ($) {
	'use strict';

	/**
	 * All of the code for plugin admin JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed we will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables us to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 */

    $(document).ready(function() {
        $('#smarty_speedy_manual_update').click(function() {
            $.post(speedy_params.ajax_url, {
                action: 'smarty_trigger_speedy_update',
                security: speedy_params.update_nonce
            }, function(response) {
                if (response.success) {
                    $('#smarty_speedy_update_message').html('<div class="notice notice-info smarty-auto-hide-notice" style="width:250px;"><p>' + response.data.message + '</p></div>');
                } else {
                    $('#smarty_speedy_update_message').html('<div class="notice notice-error smarty-auto-hide-notice" style="width:250px;"><p>' + speedy_params.updatespeedyOfficesFailedMessage + '</p></div>');
                }
            });
        });
    });

	$(document).on('DOMNodeInserted', function(e) {
		if ($(e.target).hasClass('smarty-auto-hide-notice')) {
			setTimeout(function() {
				$(e.target).fadeTo(500, 0).slideUp(500, function() {
					$(this).remove(); 
				});
			}, 3000); // time (in milliseconds)
		}
	});
})(jQuery);