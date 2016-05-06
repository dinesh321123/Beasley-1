jQuery(function () {

	window.GreaterMediaFeatureImagePreferenceAdmin = (function () {

		/**
		 * Returns a translated description of a age restriction
		 *
		 * @param string feature_image_preference
		 *
		 * @return string description
		 */
		function feature_image_preference_description(feature_image_preference) {

			if ('string' !== typeof feature_image_preference) {
				return ('undefined' !== typeof GreaterMediaFeatureImagePreference) ? GreaterMediaFeatureImagePreference.strings['No restriction'] : 'No restriction';
			}

			if ('poster' === feature_image_preference) {
				return ('undefined' !== typeof GreaterMediaFeatureImagePreference) ? GreaterMediaFeatureImagePreference.strings['Poster'] : 'Poster';
			} else if ('top' === feature_image_preference) {
				return ('undefined' !== typeof GreaterMediaFeatureImagePreference) ? GreaterMediaFeatureImagePreference.strings['Top'] : 'Top';
      } else if ('inline' === feature_image_preference) {
				return ('undefined' !== typeof GreaterMediaFeatureImagePreference) ? GreaterMediaFeatureImagePreference.strings['Inline'] : 'Inline';
      } else {
				return ('undefined' !== typeof GreaterMediaFeatureImagePreference) ? GreaterMediaFeatureImagePreference.strings['None'] : 'None';
			}

		}

		// Implement the postbox feature
		var feature_image_preference_div = jQuery('#featureimagepreferencediv');
		feature_image_preference_div.html(GreaterMediaFeatureImagePreference.templates.feature_image_preference);

		// Show the radio buttons
		jQuery("a[href='#edit_feature_image_preference']").click(function () {

			feature_image_preference_div.slideDown();

			if (true !== feature_image_preference_div.data('populated')) {

				feature_image_preference_div.find('input').filter('[name=fip_status]').filter('[value="' + jQuery('#hidden_feature_image_preference').val() + '"]').attr('checked', 'checked');

				feature_image_preference_div.data('populated', true);
			}

		});

		// Cancel button
		feature_image_preference_div.find('.cancel-feature-image-preference').click(function () {

			feature_image_preference_div.find('input').filter('[name=fip_status]').filter('[value="' + jQuery('#hidden_feature_image_preference').val() + '"]').attr('checked', 'checked');

			feature_image_preference_div.slideUp();

		});

		// Update hidden fields
		feature_image_preference_div.find('.save-feature-image-preference').click(function () {

			var checked_option = feature_image_preference_div.find('input').filter('[name=fip_status]').filter(':checked');

			jQuery('#hidden_feature_image_preference').val(checked_option.val());

			jQuery('#featureimagepreferencediv').slideUp();

			jQuery('#feature-image-preference-value').find('b').text(checked_option.parent().text());

		});

	})();

});
