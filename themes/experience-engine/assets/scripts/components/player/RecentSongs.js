import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

class RecentSongs extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { isOpen: false };
		self.recentSongsModalRef = React.createRef();

		self.onToggle = self.handleToggleClick.bind( self );
		self.handleEscapeKeyDown = self.handleEscapeKeyDown.bind( self );
		self.handleUserEventOutside = self.handleUserEventOutside.bind( self );
	}

	componentDidMount() {
		document.addEventListener( 'mousedown', this.handleUserEventOutside, false );
		document.addEventListener( 'scroll', this.handleUserEventOutside, false );
		document.addEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	componentWillUnmount() {
		document.removeEventListener( 'mousedown', this.handleUserEventOutside, false );
		document.removeEventListener( 'scroll', this.handleUserEventOutside, false );
		document.removeEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	handleToggleClick() {
		this.setState( prevState => ( { isOpen: !prevState.isOpen } ) );
	}

	handleUserEventOutside( e ) {
		const self = this;
		const { current: ref } = self.recentSongsModalRef;

		if ( !ref || !ref.contains( e.target ) ) {
			self.setState( { isOpen: false } );
		}
	}

	handleEscapeKeyDown( e ) {
		if ( 27 === e.keyCode ) {
			this.setState( { isOpen: false } );
		}
	}

	render() {
		const self = this;
		const { isOpen } = self.state;
		const { songs, colors } = self.props;

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
			<div ref={self.recentSongsModalRef} className={`controls-recent${isOpen ? ' -open' : ''}`}>
				<button onClick={self.onToggle}>
					<svg width="29" height="6" viewBox="0 0 28 6" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="6" height="6" rx="3" fill="#EB108B" style={colors}/>
						<rect width="6" height="6" rx="3" fill="#4898D3" style={colors}/>
						<rect width="6" height="6" rx="3" fill="#707070" style={colors}/>
						<rect x="11" width="6" height="6" rx="3" fill="#EB108B" style={colors}/>
						<rect x="11" width="6" height="6" rx="3" fill="#4898D3" style={colors}/>
						<rect x="11" width="6" height="6" rx="3" fill="#707070" style={colors}/>
						<rect x="22" width="6" height="6" rx="3" fill="#EB108B" style={colors}/>
						<rect x="22" width="6" height="6" rx="3" fill="#4898D3" style={colors}/>
						<rect x="22" width="6" height="6" rx="3" fill="#707070" style={colors}/>
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
