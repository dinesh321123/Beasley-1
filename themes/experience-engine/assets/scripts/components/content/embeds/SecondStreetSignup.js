import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreetSignup extends PureComponent {
	componentDidMount() {
		const { placeholder, designid } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const element = document.createElement('script');

		element.setAttribute(
			'src',
			'https://embed.secondstreetapp.com/Scripts/dist/optin.js',
		);

		element.setAttribute('data-ss-embed', 'optin');
		element.setAttribute('data-design-id', designid);

		container.appendChild(element);
	}

	render() {
		return false;
	}
}

SecondStreetSignup.propTypes = {
	placeholder: PropTypes.string.isRequired,
	designid: PropTypes.string,
};

SecondStreetSignup.defaultProps = {
	designid: '',
};

export default SecondStreetSignup;
