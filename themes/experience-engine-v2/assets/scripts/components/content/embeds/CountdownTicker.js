import React from 'react';
import PropTypes from 'prop-types';

const CountdownTicker = ({ number, timeStyle }) => {
	const newNumber = `0${number}`.slice(-2);

	return (
		<>
			<div className="tick">
				<div className="up">
					<div className="shadow" />
					<div className="inn" style={timeStyle}>
						{newNumber[0]}
					</div>
				</div>
				<div className="down">
					<div className="shadow" />
					<div className="inn" style={timeStyle}>
						{newNumber[0]}
					</div>
				</div>
			</div>
			<div className="tick">
				<div className="up">
					<div className="shadow" />
					<div className="inn" style={timeStyle}>
						{newNumber[1]}
					</div>
				</div>
				<div className="down">
					<div className="shadow" />
					<div className="inn" style={timeStyle}>
						{newNumber[1]}
					</div>
				</div>
			</div>
		</>
	);
};

CountdownTicker.propTypes = {
	number: PropTypes.number,
	timeStyle: PropTypes.shape({}),
};

CountdownTicker.defaultProps = {
	number: 0,
	timeStyle: PropTypes.shape({}),
};

export default CountdownTicker;
