/**
 * <LoadingAjaxContent displayText="Loading..." />
 *
 * Loading component useful during AJAX requests
 * for additional content.
 */
import React from 'react';
import PropTypes from 'prop-types';

const LoadingAjaxContent = ({ displayText }) => (
	<div className="loading-ajax-content">
		<span className="loading-ajax-content__spinner" />
		{displayText}
	</div>
);

LoadingAjaxContent.propTypes = {
	displayText: PropTypes.string,
};

LoadingAjaxContent.defaultProps = {
	displayText: '',
};

export default LoadingAjaxContent;
