import React, { Component } from 'react';
import PropTypes from 'prop-types';

/**
 * OutbrainWidget Component
 *
 * This component renders an Outbrain widget. It dynamically loads the Outbrain script and initializes the widget.
 */
class OutbrainWidget extends Component {
	/**
	 * componentDidMount
	 *
	 * This method is called after the component is added to the DOM.
	 * It loads the Outbrain script dynamically and initializes the widget.
	 */
	componentDidMount() {
		const { url } = this.props;
		// Check if the script element already exists
		if (!document.getElementById('outbrain-widget-script')) {
			// Load the Outbrain script dynamically
			const script = document.createElement('script');
			script.type = 'text/javascript';
			script.async = true;
			script.src = '//widgets.outbrain.com/outbrain.js';
			script.id = 'outbrain-widget-script';

			// Add the script to the document's head
			document.head.appendChild(script);

			// Ensure that the script is loaded before rendering the widget
			script.onload = () => {
				// Initialize the Outbrain widget
				window._Outbrain = window._Outbrain || [];
				window._Outbrain.push({
					widget: 'AR_1',
					placement: url,
					async: true,
				});
			};
		}
	}

	/**
	 * componentWillUnmount
	 *
	 * This method is called when the component is about to be removed from the DOM.
	 * It cleans up the Outbrain widget script to avoid memory leaks.
	 */
	componentWillUnmount() {
		// Clean up the Outbrain widget script when the component is unmounted
		const script = document.getElementById('outbrain-widget-script');
		console.log(script);
		console.log('outbrain-widget-script');
		if (script) {
			document.head.removeChild(script);
		}
	}

	/**
	 * render
	 *
	 * This method renders the JSX to display the Outbrain widget.
	 */
	render() {
		const { url } = this.props;
		return <div className="OUTBRAIN" data-src={url} data-widget-id="AR_1" />;
	}
}

OutbrainWidget.propTypes = {
	url: PropTypes.string,
};

OutbrainWidget.defaultProps = {
	url: '/',
};

export default OutbrainWidget;
