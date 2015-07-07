(function($) {

	var CountryStateFinder = function() {
		this.geodata = window.__geodata__;
	};

	CountryStateFinder.prototype = {

		getCountries: function() {
			if (!this.countries) {
				this.countries = this.findCountries();
			}

			return this.countries;
		},

		getStates: function(countryCode, field) {
			var country = this.findCountry(countryCode, field);

			if (country) {
				if (!country.states) {
					country.states = this.findStates(country);
				}

				return country.states;
			}

			return [];
		},

		findCountries: function() {
			var i;
			var n         = this.geodata.length;
			var countries = [];
			var record;

			for ( i = 0; i < n; i++ ) {
				record = this.geodata[i];
				countries.push(
					{ label: record[0], value: record[1], record: record }
				);
			}

			return countries;
		},

		findCountry: function(countryCode, field) {
			var countries = this.getCountries();
			var n         = countries.length;
			var country;

			for (var i = 0; i < n; i++) {
				country = countries[i];
				if (country[field] === countryCode) {
					return country;
				}
			}

			return null;
		},

		findStates: function(country) {
			var record        = country.record;
			var statesData    = record[2];
			var statesRecords = statesData.split('|');
			var states        = [];
			var n             = statesRecords.length;
			var statesRecord, parts;

			for (var i = 0; i < n; i++) {
				statesRecord = statesRecords[i];
				parts = statesRecord.split('~');

				if (parts.length === 2) {
					stateName = parts[0];
					stateCode = parts[1];
				} else {
					stateName = stateCode = statesRecord;
				}

				states.push({ label: stateName, value: stateCode });
			}

			return states;
		}

	};

	var CountryStateGroup    = function(country_id, state_id) {
		this.$country        = $('#' + country_id);
		this.$state          = $('#' + state_id);

		var $countryHidden = $('#' + country_id + '-hidden');
		var $stateHidden = $('#' + state_id + '-hidden');

		if ($countryHidden.length > 0) {
			this.selectedCountry = $countryHidden.val();
			this.selectedState   = $stateHidden.val();

			$countryHidden.remove();
			$stateHidden.remove();
		} else {
			this.selectedCountry = null;
			this.selectedState = null;
		}

		//console.log('CountryStateGroup country: ', this.selectedCountry, ', state', this.selectedState);

		this.finder = new CountryStateFinder();
		this.subscribe();
	};

	CountryStateGroup.prototype = {

		render: function() {
			this.renderCountry();
			this.renderState();
		},

		renderCountry: function() {
			var $countryOptions = this.getCountryOptions();
			this.$country.html($countryOptions.html());
		},

		renderState: function() {
			var $stateOptions = this.getStateOptions();
			this.$state.html($stateOptions.html());
		},

		subscribe: function() {
			this.$country.on('change', $.proxy(this.didCountrySelectionChange, this));
			this.$state.on('change', $.proxy(this.didStateSelectionChange, this));
		},

		didCountrySelectionChange: function(event) {
			var $target = $(event.target);
			var value   = $target.val();

			this.selectedCountry = value;
			this.selectedState = null;
			this.renderState();
		},

		getCountryOptions: function() {
			var $select   = $('<select></select>');
			var options   = [];
			var countries = this.finder.getCountries();
			var n         = countries.length;
			var i;
			var country, $option, $firstOption;
			var mainOptions = [];
			var $cursor;
			var mainGroupCountries = {
				'US': true,
				'CA': true
			};

			$option = $firstOption = this.getOption('Select Country', '', !!this.selectedCountry);
			$select.append($option);

			$option = $cursor = this.getOption( '─────────────────────────', '' );
			$option.attr('disabled', 'disabled');
			$select.append($option);

			for (i = 0; i < n; i++) {
				country = countries[i];
				$option = this.getOption(
					country.label,
					country.label,
					country.label === this.selectedCountry
				);

				if ( mainGroupCountries[country.value] ) {
					mainOptions.push($option);
				} else {
					$select.append($option);
				}
			}

			for (i = 0; i < mainOptions.length; i++) {
				$option = mainOptions[i];
				$cursor.after($option);
				$cursor = $option;
			}

			$option = this.getOption( '─────────────────────────', '' );
			$option.attr('disabled', 'disabled');
			$cursor.after($option);

			return $select;
		},

		getStateOptions: function() {
			var $select = $('<select></select>');
			var options   = [];
			var states = this.finder.getStates(this.selectedCountry, 'label');
			var n = states.length;
			var i;
			var state, $option;

			$option = this.getOption('Select State/Province', '', !!this.selectedState);
			$select.append($option);

			for (i = 0; i < n; i++) {
				state = states[i];
				$option = this.getOption(
					state.label,
					state.value,
					state.value === this.selectedState
				);

				$select.append($option);
			}

			return $select;
		},

		getOption: function(label, value, selected) {
			$option = $('<option></option>');

				$option.attr('value', value);

			$option.text(label);

			if (selected) {
				$option.attr('selected', 'selected');
			}

			return $option;
		},

		getSelectedCountryCode: function() {
			var countries = this.finder.getCountries();
			var n = countries.length;
			var i, country;

			for (i = 0; i < n; i++) {
				country = countries[i];
				if (country.label === this.selectedCountry) {
					return country.value;
				}
			}

			return null;
		}

	};

	var ZipCodeValidator = function() {

	};

	ZipCodeValidator.prototype = {

		patterns: {
			"US": /^\d{5}([ \-]\d{4})?$/,
			"GB": /^[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z]{2}$/,
			"CA": /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/,
			"DE": /^(?!01000|99999)(0[1-9]\d{3}|[1-9]\d{4})$/,
			"IN": /^[1-9]{1}[0-9]{2}\s{0,1}[0-9]{3}$/,

			"JE": /^JE\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}$/,
			"GG": /^GY\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}$/,
			"IM": /^IM\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}$/,
			"JP": /^\d{3}-\d{4}$/,
			"FR": /^\d{2}[ ]?\d{3}$/,
			"AU": /^\d{4}$/,
			"IT": /^\d{5}$/,
			"CH": /^\d{4}$/,
			"AT": /^\d{4}$/,
			"ES": /^\d{5}$/,
			"NL": /^\d{4}[ ]?[A-Z]{2}$/,
			"BE": /^\d{4}$/,
			"DK": /^\d{4}$/,
			"SE": /^\d{3}[ ]?\d{2}$/,
			"NO": /^\d{4}$/,
			"BR": /^\d{5}[\-]?\d{3}$/,
			"PT": /^\d{4}([\-]\d{3})?$/,
			"FI": /^\d{5}$/,
			"AX": /^22\d{3}$/,
			"KR": /^\d{3}[\-]\d{3}$/,
			"CN": /^\d{6}$/,
			"TW": /^\d{3}(\d{2})?$/,
			"SG": /^\d{6}$/,
			"DZ": /^\d{5}$/,
			"AD": /^AD\d{3}$/,
			"AR": /^([A-HJ-NP-Z])?\d{4}([A-Z]{3})?$/,
			"AM": /^(37)?\d{4}$/,
			"AZ": /^\d{4}$/,
			"BH": /^((1[0-2]|[2-9])\d{2})?$/,
			"BD": /^\d{4}$/,
			"BB": /^(BB\d{5})?$/,
			"BY": /^\d{6}$/,
			"BM": /^[A-Z]{2}[ ]?[A-Z0-9]{2}$/,
			"BA": /^\d{5}$/,
			"IO": /^BBND 1ZZ$/,
		},

		validate: function(zip, country) {
			var pattern = this.patterns[country];

			if (pattern) {
				return pattern.test(zip);
			} else {
				/* TODO: if no pattern found, no validation at the moment */
				return true;
			}
		}

	};

	window.CountryState = {
		Finder           : CountryStateFinder,
		Group            : CountryStateGroup,
		ZipCodeValidator : ZipCodeValidator,
	};

}(jQuery));
