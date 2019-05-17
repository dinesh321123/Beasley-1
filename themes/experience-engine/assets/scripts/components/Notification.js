import React from 'react';
import { PropTypes } from 'prop-types';

function Notification( { message } ) {
	return (
		<div className="breaking-news-banner -fade">
			<div className="breaking-news-banner__inner">
				<span className="breaking-news-banner__excerpt">{message}</span>
			</div>
		</div>
	);
}

Notification.propTypes = {
	message: PropTypes.string
};

export default Notification;
