
jQuery(document).ready(function() {
	
	jQuery('.twintcw-transaction-table .twintcw-more-details-button').each(function() {
		jQuery(this).click(function() {
			
			// hide all open 
			jQuery('.twintcw-transaction-table').find('.active').removeClass('active');
			
			// Get transaction ID
			var mainRow = jQuery(this).parents('.twintcw-main-row');
			var transactionId = mainRow.attr('id').replace('twintcw-_main_row_', '');
			
			var selector = '.twintcw-transaction-table #twintcw_details_row_' + transactionId;
			jQuery(selector).addClass('active');
			jQuery(mainRow).addClass('active');
		})
	});
	
	jQuery('.twintcw-transaction-table .twintcw-less-details-button').each(function() {
		jQuery(this).click(function() {
			// hide all open 
			jQuery('.twintcw-transaction-table').find('.active').removeClass('active');
		})
	});
	
	jQuery('.twintcw-transaction-table .transaction-information-table .description').each(function() {
		jQuery(this).mouseenter(function() {
			jQuery(this).toggleClass('hidden');
		});
		jQuery(this).mouseleave(function() {
			jQuery(this).toggleClass('hidden');
		})
	});
	
});