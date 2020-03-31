import React from 'react';
import PropTypes from 'prop-types';

const MyEmbed = ({ text }) => {
	return (
		<div className="my-embed">
			<h3>{text}</h3>
		</div>
	);
};

MyEmbed.propTypes = {
	text: PropTypes.string,
};

MyEmbed.defaultProps = {
	text: '',
};

export default MyEmbed;
