import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreetPreferenceCenter extends PureComponent {
	componentDidMount() {
		const { placeholder, orgid } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const element = document.createElement('script');

		element.setAttribute(
			'src',
			'https://embed.secondstreetapp.com/Scripts/dist/preferences.js',
		);

		element.setAttribute('data-ss-embed', 'preferences');
		element.setAttribute('data-organization-id', orgid);

		container.appendChild(element);
	}

	render() {
		return false;
	}
}

SecondStreetPreferenceCenter.propTypes = {
	placeholder: PropTypes.string.isRequired,
	orgid: PropTypes.string,
};

SecondStreetPreferenceCenter.defaultProps = {
	orgid: '',
};

export default SecondStreetPreferenceCenter;
