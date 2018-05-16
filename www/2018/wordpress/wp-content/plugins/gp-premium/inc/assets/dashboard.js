jQuery( document ).ready(function( $ ) {
	$( '#generate-select-all' ).on( 'click', function( event ) {
		if ( this.checked ) {
			$( '.addon-checkbox' ).each( function() {
				this.checked = true;
			});
		} else {
			$( '.addon-checkbox' ).each( function() {
				this.checked = false;
			});
		}
	} );

	$( '#generate_license_key_gp_premium' ).on( 'input', function() {
		if ( '' !== $.trim( this.value ) ) {
			$( '.beta-testing-container' ).show();
		} else {
			$( '.beta-testing-container' ).hide();
		}
	} );
});
