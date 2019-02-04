import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

class RecentSongs extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { isOpen: false };

		self.onToggle = self.handleToggleClick.bind( self );
	}

	handleToggleClick() {
		this.setState( prevState => ( { isOpen: !prevState.isOpen } ) );
	}

	render() {
		const self = this;
		const { isOpen } = self.state;
		const { songs } = self.props;
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

				time = <span className="time-played">{time}</span>;
			}

			return (
				<li key={song.cueTimeStart}>
					<span className="cue-point-artist">{song.artistName}</span>
					<span className="cue-point-title">{song.cueTitle}</span>
					{time}
				</li>
			);
		} );

		return (
			<div className={`controls-recent${isOpen ? ' -open' : ''}`}>
				<button onClick={self.onToggle}>
					<svg width="29" height="6" viewBox="0 0 28 6" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="6" height="6" rx="3" fill="#EB108B"/>
						<rect width="6" height="6" rx="3" fill="#4898D3"/>
						<rect width="6" height="6" rx="3" fill="#707070"/>
						<rect x="11" width="6" height="6" rx="3" fill="#EB108B"/>
						<rect x="11" width="6" height="6" rx="3" fill="#4898D3"/>
						<rect x="11" width="6" height="6" rx="3" fill="#707070"/>
						<rect x="22" width="6" height="6" rx="3" fill="#EB108B"/>
						<rect x="22" width="6" height="6" rx="3" fill="#4898D3"/>
						<rect x="22" width="6" height="6" rx="3" fill="#707070"/>
					</svg>
				</button>

				<div className="controls-recent-songs">
					<h5>Recently played</h5>
					<ul>
						{items}
					</ul>
				</div>
			</div>
		);
	}

}

RecentSongs.propTypes = {
	songs: PropTypes.arrayOf( PropTypes.object ).isRequired,
};

function mapStateToProps( { player } ) {
	return {
		songs: player.songs,
	};
}

export default connect( mapStateToProps )( RecentSongs );
