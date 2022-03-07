import React from 'react';
import PropTypes from 'prop-types';

/**
 * Displays a notification message at the top of the page.
 *
 * @param {object} props
 */
const Notification = ({ message, noticeClass }) => {
	return (
		<div
			className={`breaking-news-banner notification-banner -fade ${noticeClass}`}
		>
			<div className="breaking-news-banner__inner">
				<span
					className="breaking-news-banner__excerpt"
					dangerouslySetInnerHTML={{ __html: message }}
				/>
			</div>
		</div>
	);
};

Notification.propTypes = {
	message: PropTypes.string,
	noticeClass: PropTypes.string,
};

Notification.defaultProps = {
	message: '',
	noticeClass: '',
};

export default Notification;
