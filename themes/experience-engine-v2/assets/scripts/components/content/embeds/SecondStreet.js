import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreet extends PureComponent {
	getLastSecondStreetHeight() {
		return window.lastSecondStreetHeight;
	}

	setLastSecondStreetHeight(value) {
		window.lastSecondStreetHeight = value;
	}

	componentDidMount() {
		const { placeholder, script, embed, opguid, routing } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const beasleyIFrameElement = document.createElement('iframe');
		beasleyIFrameElement.height = this.getLastSecondStreetHeight()
			? `${this.getLastSecondStreetHeight()}px`
			: '0';
		beasleyIFrameElement.style.width = '100%';
		beasleyIFrameElement.style.border = 0;
		container.appendChild(beasleyIFrameElement);
		beasleyIFrameElement.contentWindow.SecondStreetSDK = {
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

		const beasleyIFrameContentDoc = beasleyIFrameElement.contentDocument
			? beasleyIFrameElement.contentDocument
			: beasleyIFrameElement.contentWindow.document;

		const beasleyIFrameDocBody = beasleyIFrameContentDoc.getElementsByTagName(
			'body',
		)[0];
		beasleyIFrameDocBody.style.margin = 0;

		// Observe When SS Adds Children And Assume First Child Is SS IFrame.
		const beasleyIFrameObserver = new MutationObserver(
			(mutations, observer) => {
				console.log('beasleyIFrameObserver: ', mutations, observer);

				// Check That First Added Node Of First Mutation Is An IFrame
				if (
					!mutations ||
					!mutations[0].addedNodes ||
					mutations[0].addedNodes[0].nodeName !== 'IFRAME'
				) {
					console.log(
						'Second Street Modified Beasley IFrame Without An Inner IFrame',
					);
					return;
				}

				const ssIFrameElement = mutations[0].addedNodes[0];

				// SS Modifies History by adding same page twice and also causes the first Back() to do nothing.
				// Our work-around is to fire a silent Back() after SS Renders which corrects our History.
				// We observe SS IFrame Height Being Changed And After No Activity For A Second, Schedule Back() In One More Second.
				// NOTE: These time limits are conservative because if we fire Back() too soon, it will not be Silent.
				let ssResetHeightTimeout;
				let ssSilentBackTimeout;
				const ssIFrameObserver = new MutationObserver((mutations, observer) => {
					if (ssIFrameElement.clientHeight) {
						console.log(
							`SS Reported IFrame HEIGHT: ${ssIFrameElement.clientHeight}`,
						);

						if (ssResetHeightTimeout) {
							window.clearTimeout(ssResetHeightTimeout);
						}
						ssResetHeightTimeout = setTimeout(() => {
							// NOTE - MUST FIRE After Full SS Render, But If User Quickly Clicks Back It Might Be Funky
							if (ssSilentBackTimeout) {
								window.clearTimeout(ssSilentBackTimeout);
							}
							ssSilentBackTimeout = setTimeout(() => {
								const newHeight = ssIFrameElement.clientHeight + 20; // Add 20 Extra To Account For SS Being Short
								console.log(
									`Waited 1.5 Secs Since Last SS Element Added. Correcting SS IFrame Height To ${newHeight} & Finalizing`,
								);
								ssIFrameObserver.disconnect();
								this.setLastSecondStreetHeight(newHeight); // Save For Next SS Render And Avoid Page Shift
								beasleyIFrameElement.height = newHeight;
								ssIFrameElement.style.height = `${newHeight}px`;
								window.history.back(); // Back out of the history changes caused by SS
							}, 1500);
						}, 500);
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

		beasleyIFrameObserver.observe(beasleyIFrameDocBody, {
			childList: true,
		});

		// Now Add Second Street JS to Beasley IFrame Container
		setTimeout(() => {
			const scriptElement = beasleyIFrameContentDoc.createElement('script');
			scriptElement.setAttribute('data-ss-embed', embed);
			scriptElement.setAttribute('data-opguid', opguid);
			scriptElement.setAttribute('data-routing', routing);
			scriptElement.setAttribute('src', script);

			beasleyIFrameDocBody.appendChild(scriptElement);
		}, 0);
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
