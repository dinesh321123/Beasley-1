import React, { PureComponent } from 'react';
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
		self.handleViewMoreClick = self.handleViewMoreClick.bind( self );
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

	handleViewMoreClick() {
		this.setState( { isOpen: false } );
	}

	render() {
		const self = this;
		const { isOpen } = self.state;
		const { songs, colors } = self.props;

		if ( !Array.isArray( songs ) || !songs.length ) {
			return false;
		}

		const buttonsFillStyle = {
			fill: colors['--brand-button-color'] || colors['--global-theme-secondary'],
			stroke: colors['--brand-button-color'] || colors['--global-theme-secondary'],
		};

		const h5Style = {
			color: colors['--brand-text-color'],
		};

		const modalStyle = {
			background: colors['--brand-background-color'],
			color: colors['--brand-text-color'],
		};

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

		let config = window.bbgiconfig;
		let callsign = '';
		let viewMoreLink = '';

		if ( config.streams && 0 < config.streams.length ) {
			callsign     = config.streams[0].stream_call_letters;
			viewMoreLink = '/stream/' + callsign + '/';
		}

		return (
			<div ref={self.recentSongsModalRef} className={`controls-recent${isOpen ? ' -open' : ''}`}>
				<button onClick={self.onToggle}>
					<svg width="29" height="6" viewBox="0 0 28 6" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="6" height="6" rx="3" fill="#EB108B" style={buttonsFillStyle}/>
						<rect width="6" height="6" rx="3" fill="#4898D3" style={buttonsFillStyle}/>
						<rect width="6" height="6" rx="3" fill="#707070" style={buttonsFillStyle}/>
						<rect x="11" width="6" height="6" rx="3" fill="#EB108B" style={buttonsFillStyle}/>
						<rect x="11" width="6" height="6" rx="3" fill="#4898D3" style={buttonsFillStyle}/>
						<rect x="11" width="6" height="6" rx="3" fill="#707070" style={buttonsFillStyle}/>
						<rect x="22" width="6" height="6" rx="3" fill="#EB108B" style={buttonsFillStyle}/>
						<rect x="22" width="6" height="6" rx="3" fill="#4898D3" style={buttonsFillStyle}/>
						<rect x="22" width="6" height="6" rx="3" fill="#707070" style={buttonsFillStyle}/>
					</svg>
				</button>

				<div className="controls-recent-songs" style={modalStyle}>
					<h5 style={h5Style}>Recently played</h5>
					<ul>
						{items}
					</ul>
					<a href={viewMoreLink} onClick={this.handleViewMoreClick}>View More</a>
				</div>
			</div>
		);
	}

}

function mapStateToProps( { player } ) {
	return {
		songs: player.songs,
	};
}

export default connect( mapStateToProps )( RecentSongs );
