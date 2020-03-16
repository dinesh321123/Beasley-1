import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

class RecentSongs extends PureComponent {
	constructor(props) {
		super(props);

		this.state = { isOpen: false };
		this.recentSongsModalRef = React.createRef();

		this.onToggle = this.handleToggleClick.bind(this);
		this.handleEscapeKeyDown = this.handleEscapeKeyDown.bind(this);
		this.handleUserEventOutside = this.handleUserEventOutside.bind(this);
		this.handleViewMoreClick = this.handleViewMoreClick.bind(this);
	}

	componentDidMount() {
		document.addEventListener('mousedown', this.handleUserEventOutside, false);
		document.addEventListener('scroll', this.handleUserEventOutside, false);
		document.addEventListener('keydown', this.handleEscapeKeyDown, false);
	}

	componentWillUnmount() {
		document.removeEventListener(
			'mousedown',
			this.handleUserEventOutside,
			false,
		);
		document.removeEventListener('scroll', this.handleUserEventOutside, false);
		document.removeEventListener('keydown', this.handleEscapeKeyDown, false);
	}

	handleToggleClick() {
		this.setState(prevState => ({ isOpen: !prevState.isOpen }));
	}

	handleUserEventOutside(e) {
		const { current: ref } = this.recentSongsModalRef;

		if (!ref || !ref.contains(e.target)) {
			this.setState({ isOpen: false });
		}
	}

	handleEscapeKeyDown(e) {
		if (e.keyCode === 27) {
			this.setState({ isOpen: false });
		}
	}

	handleViewMoreClick() {
		this.setState({ isOpen: false });
	}

	render() {
		const { isOpen } = this.state;
		const { songs, colors } = this.props;

		if (!Array.isArray(songs) || !songs.length) {
			return false;
		}

		const buttonsFillStyle = {
			fill:
				colors['--brand-button-color'] || colors['--global-theme-secondary'],
			stroke:
				colors['--brand-button-color'] || colors['--global-theme-secondary'],
		};

		const h5Style = {
			color: colors['--brand-text-color'],
		};

		const modalStyle = {
			background: colors['--brand-background-color'],
			color: colors['--brand-text-color'],
		};

		const items = songs.map(song => {
			let time = false;
			if (song.cueTimeStart > 0) {
				time = new Date(+song.cueTimeStart);
				time = time.toLocaleString('en-US', {
					hour: 'numeric',
					minute: 'numeric',
					hour12: true,
				});

				time = <span className="time-played">{time}</span>;
			}

			return (
				<li key={song.cueTimeStart}>
					<span className="cue-point-artist">{song.artistName}</span>
					<span className="cue-point-title">{song.cueTitle}</span>
					{time}
				</li>
			);
		});

		const config = window.bbgiconfig;
		let callsign = '';
		let viewMoreLink = '';

		if (config.streams && config.streams.length > 0) {
			callsign = config.streams[0].stream_call_letters;
			viewMoreLink = `/stream/${callsign}/`;
		}

		return (
			<div
				ref={this.recentSongsModalRef}
				className={`controls-recent${isOpen ? ' -open' : ''}`}
			>
				<button onClick={this.onToggle} type="button">
					<svg
						width="29"
						height="6"
						viewBox="0 0 28 6"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
					>
						<rect
							width="6"
							height="6"
							rx="3"
							fill="#EB108B"
							style={buttonsFillStyle}
						/>
						<rect
							width="6"
							height="6"
							rx="3"
							fill="#4898D3"
							style={buttonsFillStyle}
						/>
						<rect
							width="6"
							height="6"
							rx="3"
							fill="#707070"
							style={buttonsFillStyle}
						/>
						<rect
							x="11"
							width="6"
							height="6"
							rx="3"
							fill="#EB108B"
							style={buttonsFillStyle}
						/>
						<rect
							x="11"
							width="6"
							height="6"
							rx="3"
							fill="#4898D3"
							style={buttonsFillStyle}
						/>
						<rect
							x="11"
							width="6"
							height="6"
							rx="3"
							fill="#707070"
							style={buttonsFillStyle}
						/>
						<rect
							x="22"
							width="6"
							height="6"
							rx="3"
							fill="#EB108B"
							style={buttonsFillStyle}
						/>
						<rect
							x="22"
							width="6"
							height="6"
							rx="3"
							fill="#4898D3"
							style={buttonsFillStyle}
						/>
						<rect
							x="22"
							width="6"
							height="6"
							rx="3"
							fill="#707070"
							style={buttonsFillStyle}
						/>
					</svg>
				</button>

				<div className="controls-recent-songs" style={modalStyle}>
					<h5 style={h5Style}>Recently played</h5>
					<ul>{items}</ul>
					<a href={viewMoreLink} onClick={this.handleViewMoreClick}>
						View More
					</a>
				</div>
			</div>
		);
	}
}

RecentSongs.propTypes = {
	colors: PropTypes.shape({
		'--global-theme-secondary': PropTypes.string,
		'--brand-button-color': PropTypes.string,
		'--brand-background-color': PropTypes.string,
		'--brand-text-color': PropTypes.string,
	}),
	songs: PropTypes.arrayOf(PropTypes.shape({})).isRequired,
};

RecentSongs.defaultProps = {
	colors: {},
};
function mapStateToProps({ player }) {
	return {
		songs: player.songs,
	};
}

export default connect(mapStateToProps)(RecentSongs);
