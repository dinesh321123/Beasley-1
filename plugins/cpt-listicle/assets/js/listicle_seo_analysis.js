/* global jQuery, YoastSEO, LISTICLEYoastSEO */
( ( $, fields, document ) => {
	/**
	 * The analyze module for Yoast SEO.
	 */
	var module = {
		timeout: undefined,

		// Load plugin and add hooks.
		load: () => {
			// Update Yoast SEO analyzer when fields are updated.
			fields.map( module.listenToField );

			YoastSEO.app.registerPlugin( 'ListicleCPTMetaboxes', { status: 'loading' } );
			YoastSEO.app.pluginReady( 'ListicleCPTMetaboxes' );
			YoastSEO.app.registerModification( 'content', module.addContent, 'ListicleCPTMetaboxes', 5 );

			// Make the Yoast SEO analyzer works for existing content when page loads.
			module.update();
		},
		onContentDelete: () => {
			setTimeout( () => {
				// Update SEO By Rank Math analyzer when fields are updated.
				fields.map( module.listenToField );
				module.update();
			}, 500 );
		},
		// Add content to Yoast SEO Analyzer.
		addContent: ( content ) => {
			fields.map( ( fieldName ) => {
				content += ' ' + getFieldContent( fieldName );
				console.log('content for ', fieldName, ' follows: ', content);
			} );
			return content;
		},
		// Listen to field change and update Yoast SEO analyzer.
		listenToField: ( fieldName ) => {
			var inputFields = document.getElementsByName(fieldName);
			if(inputFields.length > 0) {
				for (var i = 0; i < inputFields.length; i++) {
					var inputField = inputFields[i];
					var inputFieldID = inputField.id;
					if ( inputFieldID && isEditor( inputFieldID ) ) {
						tinymce.get( inputFieldID ).on( 'keyup', module.update );
						return;
					}
					if (inputField) {
						inputField.addEventListener( 'keyup', module.update );
					}
				}
			}
		},
		// Update the YoastSEO result. Use debounce technique, which triggers only when keys stop being pressed.
		update: () => {
			clearTimeout( module.timeout );
			module.timeout = setTimeout( () => {
				YoastSEO.app.refresh();
			}, 250 );
		}
	};

	/**
	 * Get field content.
	 * Works for normal inputs and TinyMCE editors.
	 *
	 * @param fieldName The field Name
	 * @returns string
	 */
	getFieldContent = ( fieldName ) => {
		var content = '';
		var inputFields = document.getElementsByName(fieldName);
		if(inputFields.length > 0) {
			for (var i = 0; i < inputFields.length; i++) {
				var inputField = inputFields[i];
				var inputFieldID = inputField.id;
				if (inputField) {
					var inputFieldcontent = isEditor( inputFieldID ) ? tinymce.get( inputFieldID ).getContent() : inputField.value;
					content += ' ' + (inputFieldcontent ? inputFieldcontent : '');
				}
			}
		}
		return content;
	};

	/**
	 * Check if the field is a TinyMCE editor.
	 *
	 * @param fieldId The field ID
	 * @returns boolean
	 */
	isEditor = fieldId => typeof tinymce !== 'undefined' && tinymce.get( fieldId ) !== null;

	// Run on document ready.
	if ( typeof YoastSEO !== "undefined" && typeof YoastSEO.app !== "undefined" ) {
		$( module.load );
	} else {
		$( window ).on(
			"YoastSEO:ready",
			() => {
				$( module.load );
			}
		);
	}
	// Run on add/remove clone fields
	$(document).on( 'click', '.content-delete', module.onContentDelete );
} )( jQuery, LISTICLEYoastSEO, document );
