import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class BrandedContent extends PureComponent {
	componentDidMount() {
		const { placeholder, stackid, layout } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const element = document.createElement('div');

		element.setAttribute('data-stackid', stackid);

		if (layout) {
			element.setAttribute('data-layout', layout);
		}

		element.setAttribute('class', 'dml-widget-container');

		container.appendChild(element);

		if (window.DML) {
			window.DML.dmlLoad(element);
		}
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
