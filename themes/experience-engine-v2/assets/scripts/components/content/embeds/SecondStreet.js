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

		// Look Every Half Second For Six Times For a # in URL and do a silent back
		const silentBackRoutine = backTries => {
			console.log(`Silent Back Attempt ${backTries}`);
			if (window.location.href.indexOf('#') > -1) {
				window.history.back();
			} else if (backTries < 6) {
				setTimeout(() => {
					silentBackRoutine(backTries++); // Redo if n < 5 (and pass n)
				}, 500);
			}
		};

		const beasleyIframeElement = document.createElement('iframe');
		beasleyIframeElement.height = this.getLastSecondStreetHeight()
			? `${this.getLastSecondStreetHeight()}px`
			: '0';
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

		const beasleyIFrameContentDoc = beasleyIframeElement.contentDocument
			? beasleyIframeElement.contentDocument
			: beasleyIframeElement.contentWindow.document;

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
				// Our work-around is to fire this silent Back() after SS Renders which corrects our History.
				// We observe SS IFrame Height Being Changed And After No Activity For A Second, Schedule Back() In One More Second.
				// NOTE: These time limits are conservative because if we fire Back() too soon, it will not be Silent.
				let ssResetHeightTimeout;
				let ssSilentBackTimeout;
				const ssIFrameObserver = new MutationObserver((mutations, observer) => {
					if (ssIFrameElement.clientHeight) {
						const newHeight = ssIFrameElement.clientHeight + 20; // Add 20 Extra To Account For SS Being Short
						console.log(
							`SSIFRAME HEIGHT: ${ssIFrameElement.clientHeight} - Setting Beasley Iframe Height: ${newHeight}`,
						);

						if (ssResetHeightTimeout) {
							window.clearTimeout(ssResetHeightTimeout);
						}
						ssResetHeightTimeout = setTimeout(() => {
							console.log(
								'Firing SS Height Adjust Because Height Not Changed For A Half Second',
							);
							this.setLastSecondStreetHeight(newHeight); // Save For Next SS Render And Avoid Page Shift
							beasleyIframeElement.height = newHeight;

							// Fire Silent Back() 1.5 Seconds after Last SS Height Adjust.
							// NOTE - MUST FIRE After Full SS Render, But If User Quickly Clicks Back It Might Be Funky
							if (ssSilentBackTimeout) {
								window.clearTimeout(ssSilentBackTimeout);
							}
							ssSilentBackTimeout = setTimeout(() => {
								console.log(
									'Initiating Silent Back() And Updating SS IFrame Height',
								);
								silentBackRoutine(0); // Fire Of Silent Back
								ssIFrameObserver.disconnect();
								ssIFrameElement.style.height = `${newHeight}px`;
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
