import React from 'react';
import PropTypes from 'prop-types';

const Header = ( { children } ) => (
	<div className="modal-header">
		{children}
	</div>
);

Header.propTypes = {
	children: PropTypes.oneOfType( [PropTypes.string, PropTypes.node] ).isRequired,
};

export default Header;
