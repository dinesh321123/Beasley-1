import { PureComponent } from 'react';
import PropTypes from 'prop-types';

const getDrimifyFrameIndex = () => {
	let { drimifyFrameIndex } = window;
	if (!drimifyFrameIndex) {
		window.drimifyFrameIndex = 1;
		drimifyFrameIndex = window.drimifyFrameIndex;
	} else {
		window.drimifyFrameIndex++;
		drimifyFrameIndex = window.drimifyFrameIndex;
	}

	return drimifyFrameIndex;
};

//	'<div class="drimify" data-app_url="%s" data-app_style="%s"></div>',
class DrimifyEmbed extends PureComponent {
	componentDidMount() {
		const { placeholder, app_url, app_style } = this.props;
		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		// Create div to render app
		const drimifyFrameIndex = getDrimifyFrameIndex();
		const drimifydiv = document.createElement('div');

		drimifydiv.setAttribute('id', `drimify-container-${drimifyFrameIndex}`);
		drimifydiv.setAttribute('style', 'line-height:0');
		container.appendChild(drimifydiv);

		// Loadng script for drimify
		const drimifyscript = document.createElement('script');
		drimifyscript.setAttribute(
			'src',
			'https://cdn.drimify.com/js/drimifywidget.release.min.js',
		);
		drimifyscript.onload = () => {
			// eslint-disable-next-line no-unused-vars,no-undef
			const drimifyWidget = new Drimify.Widget({
				autofocus: true,
				height: '600px',
				element: `drimify-container-${drimifyFrameIndex}`,
				engine: app_url,
				style: `height: 850px; ${app_style}`,
			});
			drimifyWidget.load();
		};

		container.appendChild(drimifyscript);
	}

	render() {
		return false;
	}
}

DrimifyEmbed.propTypes = {
	placeholder: PropTypes.string.isRequired,
	app_url: PropTypes.string,
	app_style: PropTypes.string,
};

DrimifyEmbed.defaultProps = {
	app_url: '',
	app_style: '',
};

export default DrimifyEmbed;
