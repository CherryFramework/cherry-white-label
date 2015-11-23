jQuery( function( $ ) {
		console.log(123);
	'use strict';

	if ( 'undefined' !== typeof( window.optionsPageSettings ) ) {
		if ( window.optionsPageSettings.interim_url ) {
			$( '#wp-auth-check-form' ).data( 'src', window.optionsPageSettings.interim_url );
		}
	}

	$( '[name = "visible-welcome-panel"]' ).bind( 'click', function() {
		if ( $( this ).prop( 'checked' ) ) {
			$( '#visible-to' ).show();
		} else {
			$( '#visible-to' ).hide();
		}
	} );

	$( '.upload_image_button' ).click( function() {
		var sendAttachmentBkp = wp.media.editor.send.attachment,
			button = $( this ),
			dataBrowse = $( this ).data( 'browse' );

		if ( $( button ).next().hasClass( 'remove_image_button' ) ) {
			$( button ).next().remove();
		}

		wp.media.editor.send.attachment = function( props, attachment ) {
			$( button ).prev().children( 'img' ).attr( 'src', attachment.url );
			$( '<button type="button" class="remove_image_button button button-cancel">Remove</button>' ).insertAfter( button );
			$( '[name = ' + dataBrowse + ']' ).val( attachment.url );

			wp.media.editor.send.attachment = sendAttachmentBkp;

			$( '.remove_image_button' ).click( function() {
				var r = window.confirm( 'You are sure?' ),
					src;

				if ( true === r ) {
					src = $( this ).prev().prev().children( 'img' ).attr( 'data-src' );

					$( this ).prev().prev().children( 'img' ).attr( 'src', src );
					$( this ).next().val( '' );
					$( this ).remove();
				}

				return false;
			} );
		};
		wp.media.editor.open( button );

		return false;
	});

	$( '.remove_image_button' ).click( function() {

		var r = window.confirm( 'You are sure?' ),
			src;

		if ( true === r ) {
			src = $( this ).prev().prev().children( 'img' ).attr( 'data-src' );

			$( this ).prev().prev().children( 'img' ).attr( 'src', src );
			$( this ).next().val( '' );
			$( this ).remove();
		}

		return false;
	} );

} );
