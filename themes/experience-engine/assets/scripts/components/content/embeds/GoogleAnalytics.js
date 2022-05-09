import React, { useEffect } from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { pageview } from '../../../library/google-analytics';
import { setGAPageviewData } from '../../../redux/actions/ga';

let lasturl = '';

/**
 * This embed component is responsible for triggering page view with the appropriate targeting values
 */
const GoogleAnalytics = ({
	title,
	url,
	contentgroup1,
	contentgroup2,
	dimensionkey,
	dimensionvalue,
	setGAPageviewData,
}) => {
	useEffect(() => {
		// for some reason embeds are being embed twice, this ensure we don't have double page views.
		if (lasturl !== url) {
			lasturl = url;
			pageview(title, url, {
				contentgroup1,
				contentgroup2,
				dimensionkey,
				dimensionvalue,
			});
			setGAPageviewData({
				title,
				url,
				contentgroup1,
				contentgroup2,
				dimensionkey,
				dimensionvalue,
			});
		}
	}, [url]);

	return <></>;
};

GoogleAnalytics.propTypes = {
	title: PropTypes.string.isRequired,
	url: PropTypes.string.isRequired,
	contentgroup1: PropTypes.string,
	contentgroup2: PropTypes.string,
	dimensionkey: PropTypes.string,
	dimensionvalue: PropTypes.string,
	setGAPageviewData: PropTypes.func.isRequired,
};

GoogleAnalytics.defaultProps = {
	contentgroup1: '',
	contentgroup2: '',
	dimensionkey: '',
	dimensionvalue: '',
};

function mapDispatchToProps(dispatch) {
	const actions = {
		setGAPageviewData,
	};

	return bindActionCreators(actions, dispatch);
}

export default connect(null, mapDispatchToProps)(GoogleAnalytics);
