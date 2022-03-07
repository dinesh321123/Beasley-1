import { PureComponent } from 'react';
import PropTypes from 'prop-types';

//	<div class="hsform" data-portalid="%s" data-formid="%s""></div>
class HubspotForm extends PureComponent {
	componentDidMount() {
		const { placeholder, portalid, formid } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		container.setAttribute('id', 'hsFormDiv');

		const hubspotformscript = document.createElement('script');
		hubspotformscript.setAttribute('src', 'https://js.hsforms.net/forms/v2.js');

		hubspotformscript.onload = () => {
			// eslint-disable-next-line no-undef
			hbspt.forms.create({
				portalId: portalid,
				formId: formid,
				target: '#hsFormDiv',
			});
		};

		container.appendChild(hubspotformscript);
	}

	render() {
		return false;
	}
}

HubspotForm.propTypes = {
	placeholder: PropTypes.string.isRequired,
	portalid: PropTypes.string,
	formid: PropTypes.string,
};

HubspotForm.defaultProps = {
	portalid: '',
	formid: '',
};

export default HubspotForm;
