( function( $ ) {

	$( function() {
		// add new script field
		$( document ).on( 'click', '.add-custom-script-field', function( e ) {
			e.preventDefault();

			// add script field
			$( this ).before( '<div class="custom-script-field" style="display: none;">' + $( '#custom-script-field-template' ).html() + '</div>' );

			// get last script field and display it
			$( this ).parent().find( '.custom-script-field' ).last().fadeIn( 300 );
		} );

		// remove custom script field
		$( document ).on( 'click', '.remove-custom-script-field', function( e ) {
			e.preventDefault();

			// find closest script and remove it
			$( this ).closest( '.custom-script-field' ).fadeOut( 300, function() {
				$( this ).remove();
			} );
		} );

		// add new iframe field
		$( document ).on( 'click', '.add-custom-iframe-field', function( e ) {
			e.preventDefault();

			// add iframe field
			$( this ).before( '<div class="custom-iframe-field" style="display: none;">' + $( '#custom-iframe-field-template' ).html() + '</div>' );

			// get last iframe field and display it
			$( this ).parent().find( '.custom-iframe-field' ).last().fadeIn( 300 );
		} );

		// remove custom iframe field
		$( document ).on( 'click', '.remove-custom-iframe-field', function( e ) {
			e.preventDefault();

			// find closest iframe and remove it
			$( this ).closest( '.custom-iframe-field' ).fadeOut( 300, function() {
				$( this ).remove();
			} );
		} );
	} );

} )( jQuery );
