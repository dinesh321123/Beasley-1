import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { playStation } from '../../redux/actions/player';

const Stations = ( { play } ) => {
	const { streams } = window.bbgiconfig || {};
	const stations = Object.keys( streams || {} ).map( key => (
		<div key={key}>
			<button type="button" onClick={() => play( key )}>{key}</button>
			<span>{streams[key].description}</span>
		</div>
	) );

	return (
		<div>
			<b>Stations</b>
			{stations}
		</div>
	);
};

Stations.propTypes = {
	play: PropTypes.func.isRequired,
};

const mapDispatchToProps = ( dispatch ) => bindActionCreators( { play: playStation }, dispatch );

export default connect( null, mapDispatchToProps )( Stations );
