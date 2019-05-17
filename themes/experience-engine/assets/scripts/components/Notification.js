import React from 'react';
import { PropTypes } from 'prop-types';

const Notification = ( { message } ) => {
	if ( !message || !message.length ) {
		return false;
	}

	return (
		<div className="breaking-news-banner notification-banner -fade">
			<div className="breaking-news-banner__inner">
				<span className="breaking-news-banner__excerpt">{message}</span>
			</div>
		</div>
	);
};

Notification.propTypes = {
	message: PropTypes.string
};

export default Notification;
