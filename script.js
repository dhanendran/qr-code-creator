/**
 * QR Code Creator — admin script.
 *
 * Generates QR codes locally in the browser using the bundled qr-code-styling
 * library. No data is sent to any third-party service.
 *
 * @package QRCodeCreator
 * @since   1.0.0
 */
( function ( $ ) {
	'use strict';

	var settings = window.qrCodeCreator || {};
	var i18n     = settings.i18n || {};

	var qrCode     = null;
	var mediaFrame = null;

	/**
	 * Read the current form values into a qr-code-styling options object.
	 *
	 * @return {Object}
	 */
	function buildOptions() {
		var size = parseInt( $( '#qr_code_size' ).val(), 10 ) || 200;
		var fg   = $( '#qr_code_color' ).val() || '#000000';
		var bg   = $( '#qr_code_bgcolor' ).val() || '#ffffff';
		var ecc  = $( '#qr_code_ecc' ).val() || 'M';
		var logo = $( '#qr_code_logo' ).val();

		var options = {
			width: size,
			height: size,
			type: 'canvas',
			data: $( '#qr_code_content' ).val(),
			margin: 10,
			qrOptions: {
				errorCorrectionLevel: ecc
			},
			dotsOptions: {
				color: fg,
				type: 'square'
			},
			cornersSquareOptions: {
				color: fg
			},
			cornersDotOptions: {
				color: fg
			},
			backgroundOptions: {
				color: bg
			}
		};

		if ( logo ) {
			options.image        = logo;
			options.imageOptions = {
				crossOrigin: 'anonymous',
				margin: 5,
				imageSize: 0.4,
				hideBackgroundDots: true
			};
		}

		return options;
	}

	/**
	 * Show an error message in the result column.
	 *
	 * @param {string} message Message text.
	 */
	function showError( message ) {
		$( '#qr_code_error' ).find( 'p' ).text( message );
		$( '#qr_code_error' ).show();
		$( '#qr_code_loading, #qr_code_preview, #qr_code_download' ).hide();
	}

	/**
	 * Generate (or regenerate) the QR code.
	 */
	function generate() {
		var content = $.trim( $( '#qr_code_content' ).val() );

		$( '#qr_code_error' ).hide();

		if ( ! content ) {
			showError( i18n.errorEmpty || 'Please enter some content to generate QR code.' );
			return;
		}

		if ( 'undefined' === typeof window.QRCodeStyling ) {
			showError( i18n.errorGenerate || 'Error generating QR code. Please try again.' );
			return;
		}

		try {
			var options = buildOptions();

			// Render into a fresh container each time.
			$( '#qr_code' ).empty();
			qrCode = new window.QRCodeStyling( options );
			qrCode.append( document.getElementById( 'qr_code' ) );

			$( '#qr_code_loading' ).hide();
			$( '#qr_code_preview, #qr_code_download' ).show();
		} catch ( e ) {
			showError( i18n.errorGenerate || 'Error generating QR code. Please try again.' );
		}
	}

	/**
	 * Download the current QR code in the given format.
	 *
	 * @param {string} extension png or svg.
	 */
	function download( extension ) {
		if ( ! qrCode ) {
			return;
		}

		qrCode.download( {
			name: 'qr-code',
			extension: extension
		} );
	}

	/**
	 * Reset the form and clear the result.
	 */
	function reset() {
		$( '#qr_code_content' ).val( '' );
		$( '#qr_code_size' ).val( '200' );
		$( '#qr_code_ecc' ).val( 'H' );
		$( '#qr_code_color' ).val( '#000000' );
		$( '#qr_code_bgcolor' ).val( '#ffffff' );
		$( '#qr_code_logo' ).val( '' );

		$( '#qr_code' ).empty();
		$( '#qr_code_logo_preview' ).hide().find( 'img' ).attr( 'src', '' );
		$( '#qr_code_logo_remove' ).hide();
		$( '#qr_code_loading, #qr_code_preview, #qr_code_download, #qr_code_error' ).hide();

		qrCode = null;
		$( '#qr_code_content' ).trigger( 'focus' );
	}

	/**
	 * Show the chosen logo preview.
	 *
	 * @param {string} url Image URL.
	 */
	function showLogoPreview( url ) {
		$( '#qr_code_logo_preview' ).show().find( 'img' ).attr( 'src', url );
		$( '#qr_code_logo_remove' ).show();
	}

	/**
	 * Open the WordPress media library to pick a logo.
	 */
	function chooseLogo() {
		if ( mediaFrame ) {
			mediaFrame.open();
			return;
		}

		mediaFrame = wp.media( {
			title: i18n.mediaTitle || 'Choose a logo image',
			button: { text: i18n.mediaButton || 'Use this image' },
			library: { type: 'image' },
			multiple: false
		} );

		mediaFrame.on( 'select', function () {
			var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
			$( '#qr_code_logo' ).val( attachment.url );
			showLogoPreview( attachment.url );
		} );

		mediaFrame.open();
	}

	$( function () {
		$( '#create_qr_code' ).on( 'click', generate );
		$( '#reset_qr_code' ).on( 'click', reset );

		$( '#qr_code_content' ).on( 'keypress', function ( e ) {
			if ( 13 === e.which && e.ctrlKey ) {
				generate();
			}
		} );

		$( '#qr_code_download_png' ).on( 'click', function () {
			download( 'png' );
		} );
		$( '#qr_code_download_svg' ).on( 'click', function () {
			download( 'svg' );
		} );

		$( '#qr_code_logo_upload' ).on( 'click', function ( e ) {
			e.preventDefault();
			chooseLogo();
		} );
		$( '#qr_code_logo_remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '#qr_code_logo' ).val( '' );
			$( '#qr_code_logo_preview' ).hide().find( 'img' ).attr( 'src', '' );
			$( this ).hide();
		} );
	} );
} )( jQuery );
