import React from 'react';
import PropTypes from 'prop-types';

const Alert = ( { message, type } ) => {
	if ( !message || !message.length ) {
		return false;
	}

	return (
		<div className={`modal-alert ${type}`}>
			<b>{message}</b>
		</div>
	);
};

Alert.propTypes = {
	message: PropTypes.string,
	type: PropTypes.string,
};

Alert.defaultProps = {
	message: '',
	type: 'error',
};

export default Alert;
