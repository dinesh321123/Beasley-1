import React from 'react';
import PropTypes from 'prop-types';

function Cta({ payload }) {
	if (!payload) {
		return false;
	}

	const {
		title,
		background,
		buttonColor,
		buttonText,
		buttonTextColor,
		link,
	} = payload;

	let button = false;
	if (buttonText) {
		const buttonStyle = {
			color: buttonTextColor,
			backgroundColor: buttonColor,
		};

		button = (
			<a className="btn cta-button" href={link} style={buttonStyle}>
				{buttonText}
			</a>
		);
	}

	const { color, image } = background;
	const blockStyle = {};

	if (color) {
		blockStyle.backgroundColor = color;
	}

	if (image) {
		blockStyle.backgroundImage = `url(${image})`;
	}

	return (
		<div className="cta" style={blockStyle}>
			<div className="cta-content">
				<h2 className="cta-title">{title}</h2>
				{button}
			</div>
		</div>
	);
}

Cta.propTypes = {
	payload: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
};

Cta.defaultProps = {
	payload: false,
};

export default Cta;
