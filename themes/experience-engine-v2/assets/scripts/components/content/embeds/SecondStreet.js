import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreet extends PureComponent {
	componentDidMount() {
		console.log('SS COMPONENT DID MOUNT');
		const { placeholder, script, embed, opguid, routing } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const beasleyIframeElement = document.createElement('iframe');
		beasleyIframeElement.style.width = '100%';
		beasleyIframeElement.style.border = 0;
		container.appendChild(beasleyIframeElement);
		beasleyIframeElement.contentWindow.SecondStreetSDK = {
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

		console.log('Loading SS');
		const scriptElement = document.createElement('script');
		scriptElement.setAttribute('async', true);
		scriptElement.setAttribute('src', script);
		scriptElement.setAttribute('data-ss-embed', embed);
		scriptElement.setAttribute('data-opguid', opguid);
		scriptElement.setAttribute('data-routing', routing);
		scriptElement.setAttribute('defer', '');

		const beasleyIFrameDoc = beasleyIframeElement.contentDocument
			? beasleyIframeElement.contentDocument
			: beasleyIframeElement.contentWindow.document;

		console.log('Appending Script To IFrame');
		beasleyIFrameDoc.body.style.margin = 0;
		beasleyIFrameDoc.body.appendChild(scriptElement);

		const beasleyIFrameObserver = new MutationObserver(
			(mutations, observer) => {
				console.log('beasleyIFrameObserver: ', mutations, observer);

				const ssIFrameElement = mutations[0].addedNodes[0];
				const ssIFrameObserver = new MutationObserver((mutations, observer) => {
					console.log(`SSIFRAME HEIGHT: ${ssIFrameElement.clientHeight}`);
					if (ssIFrameElement.clientHeight) {
						beasleyIframeElement.height = ssIFrameElement.clientHeight;
					}
				});
				ssIFrameObserver.observe(ssIFrameElement, {
					attributes: true,
				});

				// Once Second Street Has Added Children, We No Longer Need To Observe Beasley IFrame
				setTimeout(() => {
					beasleyIFrameObserver.disconnect();
				}, 0);
			},
		);

		beasleyIFrameObserver.observe(beasleyIFrameDoc.body, {
			childList: true,
		});
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
