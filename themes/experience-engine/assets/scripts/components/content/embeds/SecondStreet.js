import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreet extends PureComponent {
	componentDidMount() {
		const { placeholder, script, embed, opguid, routing } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		window.SecondStreetSDK = {
			version: '1.0.0',
			ready: function ready(secondstreet) {
				[
					{
						category: 'Second Street',
						name: 'secondstreet:form:visible',
						label: 'Visitor saw a Second Street form',
					},
					{
						category: 'Second Street',
						name: 'secondstreet:form:abandoned',
						label: 'Visitor abandoned a Second Street form',
					},
					{
						category: 'Second Street',
						name: 'secondstreet:form:started',
						label: 'Visitor began filling out a Second Street form',
					},
					{
						category: 'Second Street',
						name: 'secondstreet:form:submitted',
						label: 'Visitor successfully submitted a Second Street form',
					},
					{
						category: 'Second Street',
						name: 'secondstreet:formpage:error',
						label:
							'Visitor attempted to submit a page of a Second Street form but there was an error',
					},
					{
						category: 'Second Street',
						name: 'secondstreet:formpage:submitted',
						label:
							'Visitor successfully submitted a page of a Second Street form',
					},
				].forEach(function(_ref) {
					const { category } = _ref;
					const { name } = _ref;
					const { label } = _ref;
					return secondstreet.addEventListener(name, function() {
						// eslint-disable-next-line no-undef
						return window.ga('send', 'event', category, name, label);
					});
				});
				secondstreet.addEventListener('secondstreet:route:enter', function(
					data,
				) {
					// eslint-disable-next-line no-undef
					return window.ga('send', 'pageview', data.detail);
				});
			},
		};

		const element = document.createElement('script');

		element.setAttribute('async', true);
		element.setAttribute('src', script);
		element.setAttribute('data-ss-embed', embed);
		element.setAttribute('data-opguid', opguid);
		element.setAttribute('data-routing', routing);

		container.appendChild(element);
	}

	render() {
		return false;
	}
}

SecondStreet.propTypes = {
	placeholder: PropTypes.string.isRequired,
	script: PropTypes.string,
	embed: PropTypes.string,
	opguid: PropTypes.string,
	routing: PropTypes.string,
};

SecondStreet.defaultProps = {
	script: '',
	embed: '',
	opguid: '',
	routing: '',
};

export default SecondStreet;
