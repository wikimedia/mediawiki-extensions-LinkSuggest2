( function () {
	$( () => {
		/**
		 * Gets title suggestions for the currently opened wikilink
		 *
		 * @param {Object} request
		 * @param {Function} response
		 */
		function getSuggestions( request, response ) {
			let $elem = this,
				caretPos = $elem.caret( 'pos' ),
				text = $elem.val().slice( 0, Math.max( 0, caretPos ) ),
				link;

			// store caret location for currect positioning of suggestions
			$elem.data( 'caretPos', caretPos );

			if ( text.lastIndexOf( '[[' ) > text.lastIndexOf( ']]' ) ) {
				link = text.substr( text.lastIndexOf( '[[' ) + 2, caretPos );
				// store text before wikilink
				$elem.data( 'textBefore', text.slice( 0, Math.max( 0, text.lastIndexOf( '[[' ) + 2 ) ) );
				( new mw.Api() ).get( {
					action: 'opensearch',
					search: link,
					namespace: 0,
					limit: ( mw.config.get( 'wgLinkSuggestMaxSuggestions' ) || 3 )
				} ).done( ( data ) => {
					// send the data to jQuery UI autocomplete
					response( data[ 1 ] );
				} );
			}
		}

		/**
		 * Finish the wikilink with the selected suggestion
		 *
		 * @param {Object} event
		 * @param {Object} ui
		 * @return {boolean} false to prevent the contents from being overridden
		 */
		function completeLink( event, ui ) {
			const $elem = this,
				content = $elem.val(),
				link = ui.item.value,
				caretPos = $elem.data( 'caretPos' ),
				textBefore = $elem.data( 'textBefore' );
			$elem.val( textBefore + link + ']]' + content.substr( caretPos, content.length - 1 ) );

			return false;
		}

		/**
		 * Properly position the suggestions widget
		 *
		 * @param {Object} event
		 * @param {Object} ui
		 */
		function setSuggestionsPosition( event, ui ) {
			const $elem = this,
				pos = $elem.caret( 'position', $elem.data( 'caretPos' ) ),
				elemOffset = $elem.offset();

			$elem.autocomplete( 'widget' ).css( {
				top: pos.top + elemOffset.top,
				left: pos.left + elemOffset.left,
				width: 'auto',
				'max-width': 300
			} );
		}

		( mw.config.get( 'wgLinkSuggestElements' ) || [] ).forEach( ( selector, i ) => {
			const $elem = $( selector );
			$elem.autocomplete( {
				select: completeLink.bind( $elem ),
				source: getSuggestions.bind( $elem ),
				open: setSuggestionsPosition.bind( $elem )
			} );
		} );
	} );
}() );
