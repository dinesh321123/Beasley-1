import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class Audience extends PureComponent {
	componentDidMount() {
		const { placeholder, widgetid, type } = this.props;

		const container = document.getElementById(placeholder);
		if (!container || !widgetid) {
			return;
		}

		const script = document.createElement('script');
		script.src = `https://campaign.aptivada.com/sdk.js`;
		script.async = true;
		container.appendChild(script);

		const element = document.createElement('div');
		element.setAttribute('class', 'aptivada-campaign');
		container.appendChild(element);

		const customscript = document.createElement('script');
		customscript.innerHTML = `
			window.AptivadaAsyncInit = function(){
				var sdk = window.Aptivada.init({
					campaignId: ${widgetid},
					campaignType: '${type}'
				})
			}
		`;
		container.appendChild(customscript);
	}

	render() {
		return false;
	}
}

Audience.propTypes = {
	placeholder: PropTypes.string.isRequired,
	widgetid: PropTypes.string,
	type: PropTypes.string,
};

Audience.defaultProps = {
	widgetid: '',
	type: '',
};

export default Audience;
