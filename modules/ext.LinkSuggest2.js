( function ( mw, $ ) {
	$( function () {
		/**
		 * Gets title suggestions for the currently opened wikilink
		 * @param {Object} request
		 * @param {Function} response
		 */
		function getSuggestions( request, response ) {
			var $elem = this,
				caretPos = $elem.caret( 'pos' ),
				text = $elem.val().substr( 0, caretPos ),
				link;

			// store caret location for currect positioning of suggestions
			$elem.data( 'caretPos', caretPos );

			if ( text.lastIndexOf( '[[' ) > text.lastIndexOf( ']]' ) ) {
				link = text.substr( text.lastIndexOf( '[[' ) + 2, caretPos );
				// store text before wikilink
				$elem.data( 'textBefore', text.substr( 0, text.lastIndexOf( '[[' ) + 2 ) );
				( new mw.Api() ).get( {
					action: 'opensearch',
					search: link,
					namespace: 0,
					limit: ( mw.config.get( 'wgLinkSuggestMaxSuggestions' ) || 3 )
				} ).done( function ( data ) {
					// send the data to jQuery UI autocomplete
					response( data[ 1 ] );
				} );
			}
		}

		/**
		 * Finish the wikilink with the selected suggestion
		 * @param {Object} event
		 * @param {Object} ui
		 * @returns {boolean} false to prevent the contents from being overridden
		 */
		function completeLink( event, ui ) {
			var $elem = this,
				content = $elem.val(),
				link = ui.item.value,
				caretPos = $elem.data( 'caretPos' ),
				textBefore = $elem.data( 'textBefore' );
			$elem.val( textBefore + link + ']]' + content.substr( caretPos, content.length - 1 ) );

			return false;
		}

		/**
		 * Properly position the suggestions widget
		 * @param {Object} event
		 * @param {Object} ui
		 */
		function setSuggestionsPosition( event, ui ) {
			var $elem = this,
				pos = $elem.caret( 'position', $elem.data( 'caretPos' ) ),
				elemOffset = $elem.offset();

			$elem.autocomplete( 'widget' ).css( {
				'top': pos.top + elemOffset.top,
				'left': pos.left + elemOffset.left,
				'width': 'auto',
				'max-width': 300
			} );
		}

		( mw.config.get( 'wgLinkSuggestElements' ) || [] ).forEach( function ( selector, i ) {
			var $elem = $( selector );
			$elem.autocomplete( {
				select: completeLink.bind( $elem ),
				source: getSuggestions.bind( $elem ),
				open: setSuggestionsPosition.bind( $elem )
			} );
		} );
	} );
}( mediaWiki, jQuery ) );
