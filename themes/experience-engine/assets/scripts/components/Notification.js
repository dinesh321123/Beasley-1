import React from 'react';
import { PropTypes } from 'prop-types';

const Notification = ( { message, noticeClass } ) => {
	return (
		<div className={`breaking-news-banner notification-banner -fade ${noticeClass}`}>
			<div className="breaking-news-banner__inner">
				<span className="breaking-news-banner__excerpt">{message}</span>
			</div>
		</div>
	);
};

Notification.propTypes = {
	message: PropTypes.string,
	noticeClass: PropTypes.string,
};

export default Notification;
