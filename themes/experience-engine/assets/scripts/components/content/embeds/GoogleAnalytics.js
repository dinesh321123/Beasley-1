import React, { useEffect } from 'react';
import PropTypes from 'prop-types';
import { pageview } from '../../../library/google-analytics';

/**
 * This embed component is responsible for triggering page view with the appropriate targeting values
 */
const GoogleAnalytics = ( { title, contentgroup1, contentgroup2, dimensionkey, dimensionvalue } ) => {
	useEffect( () => {
		pageview( title, window.location.href, { contentgroup1, contentgroup2, dimensionkey, dimensionvalue } );
	}, [ window.location.href ] );

	return <></>;
};

GoogleAnalytics.propTypes = {
	title: PropTypes.string.isRequired,
	contentgroup1: PropTypes.string,
	contentgroup2: PropTypes.string,
	dimensionkey: PropTypes.number,
	dimensionvalue: PropTypes.string,
};

GoogleAnalytics.defaultProps = {
	contentgroup1: '',
	contentgroup2: '',
	dimensionkey: '',
	dimensionvalue: '',
};

export default GoogleAnalytics;
