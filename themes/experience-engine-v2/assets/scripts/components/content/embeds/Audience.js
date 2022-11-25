import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class Audience extends PureComponent {
	componentDidMount() {
		const { placeholder, widgetid } = this.props;

		const container = document.getElementById(placeholder);
		if (!container || !widgetid) {
			return;
		}

		const element = document.createElement('div');
		element.setAttribute('widget-id', widgetid);
		element.setAttribute('widget-type', 'app');
		element.setAttribute(
			'style',
			'background:#ffffff url(https://cdn2.aptivada.com/images/iframeLoader.gif) no-repeat center; min-height:500px;',
		);
		container.appendChild(element);
	}

	render() {
		return false;
	}
}

Audience.propTypes = {
	placeholder: PropTypes.string.isRequired,
	widgetid: PropTypes.string,
};

Audience.defaultProps = {
	widgetid: '',
};

export default Audience;
