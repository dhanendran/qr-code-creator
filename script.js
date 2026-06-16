/**
 * QR Code Creator Admin Scripts
 *
 * @package QRCodeCreator
 */

(function($) {
	'use strict';

	/**
	 * QR Code Creator Admin Object
	 */
	const QRCodeCreatorAdmin = {
		/**
		 * Initialize
		 */
		init: function() {
			this.bindEvents();
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			$( '#create_qr_code' ).on( 'click', this.handleCreateQRCode.bind( this ) );
			$( '#reset_qr_code' ).on( 'click', this.handleReset.bind( this ) );
			$( '#qr_code_content' ).on( 'keypress', function( e ) {
				if ( e.which === 13 && e.ctrlKey ) {
					$( '#create_qr_code' ).trigger( 'click' );
				}
			});
		},

		/**
		 * Handle create QR code
		 *
		 * @param {Event} e Event object
		 */
		handleCreateQRCode: function( e ) {
			e.preventDefault();

			const content = $( '#qr_code_content' ).val().trim();

			// Validate content
			if ( content.length <= 0 ) {
				this.showError( qrCodeCreator.i18n.errorEmpty );
				return;
			}

			// Get form values
			const size     = $( '#qr_code_size' ).val() || '200x200';
			const ecc      = $( '#qr_code_ecc' ).val() || 'M';
			const color    = $( '#qr_code_color' ).val() || '#000000';
			const bgcolor  = $( '#qr_code_bgcolor' ).val() || '#FFFFFF';

			// Show loading state
			this.showLoading();

			// Build API URL
			const apiUrl = this.buildApiUrl( content, size, ecc, color, bgcolor );

			// Generate preview URL (smaller size for preview)
			const previewSize = '200x200';
			const previewUrl  = this.buildApiUrl( content, previewSize, ecc, color, bgcolor );

			// Load QR code image
			this.loadQRCode( previewUrl, apiUrl );
		},

		/**
		 * Build API URL
		 *
		 * @param {string} content Content to encode
		 * @param {string} size QR code size
		 * @param {string} ecc Error correction level
		 * @param {string} color Foreground color
		 * @param {string} bgcolor Background color
		 * @return {string} API URL
		 */
		buildApiUrl: function( content, size, ecc, color, bgcolor ) {
			const baseUrl = qrCodeCreator.apiUrl || 'https://api.qrserver.com/v1/create-qr-code/';
			const params  = {
				data:    encodeURIComponent( content ),
				size:    size,
				ecc:     ecc,
				color:   color.replace( '#', '' ),
				bgcolor: bgcolor.replace( '#', '' ),
			};

			const queryString = Object.keys( params )
				.map( function( key ) {
					return encodeURIComponent( key ) + '=' + encodeURIComponent( params[ key ] );
				})
				.join( '&' );

			return baseUrl + '?' + queryString;
		},

		/**
		 * Load QR code image
		 *
		 * @param {string} previewUrl Preview image URL
		 * @param {string} downloadUrl Download image URL
		 */
		loadQRCode: function( previewUrl, downloadUrl ) {
			const img = new Image();

			img.onload = function() {
				QRCodeCreatorAdmin.hideLoading();
				QRCodeCreatorAdmin.hideError();
				QRCodeCreatorAdmin.showQRCode( previewUrl, downloadUrl );
			};

			img.onerror = function() {
				QRCodeCreatorAdmin.hideLoading();
				QRCodeCreatorAdmin.showError( qrCodeCreator.i18n.errorApi );
			};

			img.src = previewUrl;
		},

		/**
		 * Show QR code
		 *
		 * @param {string} previewUrl Preview image URL
		 * @param {string} downloadUrl Download image URL
		 */
		showQRCode: function( previewUrl, downloadUrl ) {
			$( '#qr_code' ).attr( 'src', previewUrl );
			$( '#qr_code_preview' ).fadeIn();

			// Generate download links
			const formats = [
				{ name: 'PNG', format: 'png' },
				{ name: 'JPG', format: 'jpg' },
				{ name: 'SVG', format: 'svg' },
				{ name: 'EPS', format: 'eps' },
			];

			let downloadLinks = '';
			formats.forEach( function( format ) {
				const url = downloadUrl + '&format=' + format.format;
				downloadLinks += '<a href="' + url + '" class="button" download target="_blank" rel="noopener noreferrer">' + format.name + '</a> ';
			});

			$( '#qr_code_download_link' ).html( downloadLinks );
			$( '#qr_code_download' ).fadeIn();
		},

		/**
		 * Show loading state
		 */
		showLoading: function() {
			$( '#qr_code_loading' ).show();
			$( '#qr_code_preview' ).hide();
			$( '#qr_code_download' ).hide();
			$( '#qr_code_error' ).hide();
		},

		/**
		 * Hide loading state
		 */
		hideLoading: function() {
			$( '#qr_code_loading' ).hide();
		},

		/**
		 * Show error message
		 *
		 * @param {string} message Error message
		 */
		showError: function( message ) {
			$( '#qr_code_error p' ).text( message );
			$( '#qr_code_error' ).fadeIn();
			$( '#qr_code_preview' ).hide();
			$( '#qr_code_download' ).hide();
		},

		/**
		 * Hide error message
		 */
		hideError: function() {
			$( '#qr_code_error' ).hide();
		},

		/**
		 * Handle reset
		 *
		 * @param {Event} e Event object
		 */
		handleReset: function( e ) {
			e.preventDefault();

			// Reset form
			$( '#qr_code_content' ).val( '' );
			$( '#qr_code_size' ).val( '200x200' );
			$( '#qr_code_ecc' ).val( 'M' );
			$( '#qr_code_color' ).val( '#000000' );
			$( '#qr_code_bgcolor' ).val( '#FFFFFF' );

			// Reset preview
			$( '#qr_code' ).attr( 'src', '' );
			$( '#qr_code_preview' ).hide();
			$( '#qr_code_download' ).hide();
			$( '#qr_code_download_link' ).html( '' );
			$( '#qr_code_error' ).hide();
			$( '#qr_code_loading' ).hide();

			// Focus on content field
			$( '#qr_code_content' ).focus();
		},
	};

	// Initialize when document is ready
	$( document ).ready( function() {
		QRCodeCreatorAdmin.init();
	});

})( jQuery );

