import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class BrandedContent extends PureComponent {
	componentDidMount() {
		const { placeholder, stackid, layout } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const scriptelement = document.createElement('script');
		scriptelement.setAttribute('src', 'https://c.go-fet.ch/a/embed.js');
		scriptelement.setAttribute('async', 'true');

		const element = document.createElement('div');

		element.setAttribute('data-stackid', stackid);

		if (layout) {
			element.setAttribute('data-layout', layout);
		}

		element.setAttribute('class', 'dml-widget-container');

		container.appendChild(element);
		container.appendChild(scriptelement);
	}

	render() {
		return false;
	}
}

BrandedContent.propTypes = {
	placeholder: PropTypes.string.isRequired,
	stackid: PropTypes.string,
	layout: PropTypes.string,
};

BrandedContent.defaultProps = {
	stackid: '',
	layout: '',
};

export default BrandedContent;
