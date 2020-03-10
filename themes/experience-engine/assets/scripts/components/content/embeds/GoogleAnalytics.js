import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { pageview } from '../../../library/google-analytics';

let lasturl = '';

/**
 * This embed component is responsible for triggering page view with the appropriate targeting values
 */
const GoogleAnalytics = ( { title, url, contentgroup1, contentgroup2, dimensionkey, dimensionvalue } ) => {
	useEffect( () => {
		// for some reason embeds are being embed twice, this ensure we don't have double page views.
		if ( lasturl !== url ) {
			lasturl = url;
			pageview( title, url, { contentgroup1, contentgroup2, dimensionkey, dimensionvalue } );
		}

	}, [ url ] );

	return <></>;
};

GoogleAnalytics.propTypes = {
	title: PropTypes.string.isRequired,
	url: PropTypes.string.isRequired,
	contentgroup1: PropTypes.string,
	contentgroup2: PropTypes.string,
	dimensionkey: PropTypes.string,
	dimensionvalue: PropTypes.string,
};

GoogleAnalytics.defaultProps = {
	contentgroup1: '',
	contentgroup2: '',
	dimensionkey: '',
	dimensionvalue: '',
};

export default GoogleAnalytics;
