import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

const RecentSongs = ( { songs } ) => {
	if ( !Array.isArray( songs ) || !songs.length ) {
		return false;
	}

	const items = songs.map( ( song ) => {
		let time = false;
		if ( 0 < song.cueTimeStart ) {
			time = new Date( +song.cueTimeStart );
			time = time.toLocaleString( 'en-US', {
				hour: 'numeric',
				minute: 'numeric',
				hour12: true,
			} );

			time = <span>{time}</span>;
		}

		return (
			<li key={song.cueTimeStart}>
				<span className="cue-point-title">{song.cueTitle}</span>
				<span className="cue-point-artist">{song.artistName}</span>
				{time}
			</li>
		);
	} );

	return (
		<div>
			<div>Recent Songs:</div>
			<ul>
				{items}
			</ul>
		</div>
	);
};

RecentSongs.propTypes = {
	songs: PropTypes.arrayOf( PropTypes.object ).isRequired,
};

const mapStateToProps = ( { player } ) => ( { songs: player.songs } );

export default connect( mapStateToProps )( RecentSongs );
