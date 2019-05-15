import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import dayjs from 'dayjs';

const $ = window.jQuery;

/**
 * SongArchive component fills in the song-archive-prerender element
 * with the list of songs from the Now Playing API.
 *
 * This component has a loading state, that is shown when the API
 * request is being made.
 *
 * Once the Component is mounted, the Now Playing endpoint is queried
 * and if successful the returns songs are saved to state, triggering a
 * re-render without the loading indicator.
 */
class SongArchive extends PureComponent {

	constructor( props ) {
		super( props );

		this.state = {
			loading: true,
			songs: [],
			now: Math.floor( ( new Date() ).getTime() / 1000 ),
		};
	}

	/**
	 * TODO: FE
	 * 3. Update loading text to reuse existing Loader
	 */
	render() {

		return (
			<div className="song-archive">
				<h3>Recently Played Songs on { this.props.description }</h3>

				{ this.state.loading ?
					<p>Loading ...</p>
					:
					<div>
						<h4>{ dayjs.unix( this.state.now ).format( 'MMM D, YYYY' ) }</h4>

						<ul>
							{ this.state.songs.map( ( song ) => {
								return (
									<li key={ song.id }>
										<span className="song-time">{ dayjs.unix( song.timestamp ).format( 'HH:mm A' ) }</span>
										&nbsp;
										<span className="song-title">{ song.title }</span>
										&mdash;
										<span className="song-artist">{ song.artist }</span>
									</li>
								);
							} ) }
						</ul>
					</div>
				}
			</div>
		);
	}

	componentDidMount() {
		let self = this;

		$.get( this.props.endpoint )
			.then( ( result ) => {
				self.setState( { loading: false, songs: result } );
			} )
			.fail( () => {
				self.setState( { loading: false, songs: [] } );
			} );
	}

}

SongArchive.propTypes = {
	callsign    : PropTypes.string,
	endpoint    : PropTypes.string,
	description : PropTypes.string,
};

SongArchive.defaultProps = {
	callsign    : '',
	endpoint    : '',
	description : '',
};

export default SongArchive;
