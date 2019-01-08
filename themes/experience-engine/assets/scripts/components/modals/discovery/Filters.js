import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class DiscoveryFilters extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			brand: '',
			location: '',
			genre: '',
			type: '',
			keyword: '',
		};

		self.onFilterChange = self.handleFilterChange.bind( self );
		self.onKeywordChange = self.handleKeywordChange.bind( self );
		self.onSubmit = self.handleKeywordSubmit.bind( self );
	}

	handleFilterChange( e ) {
		const self = this;
		const { target } = e;

		self.setState( { [target.name]: target.value }, () => {
			self.props.onChange( { ...self.state } );
		} );
	}

	handleKeywordChange( e ) {
		this.setState( { keyword: e.target.value } );
	}

	handleKeywordSubmit( e ) {
		e.preventDefault();
		e.stopPropagation();

		this.props.onChange( { ...this.state } );
	}

	render() {
		const self = this;
		const { brand, location, genre, type, keyword } = self.state;

		const { bbgiconfig } = window;
		const { locations, genres, publishers } = bbgiconfig || {};

		/* eslint-disable jsx-a11y/no-onchange */
		return (
			<div className="filters">
				<div className="select">
					<select name="brand" value={brand} onChange={self.onFilterChange}>
						<option value="">All Brands</option>
						{Object.keys( publishers ).map( ( item, i ) => <option key={`brand-${i}`} value={item}>{publishers[item]}</option> )}
					</select>
				</div>

				<div className="select">
					<select name="location" value={location} onChange={self.onFilterChange}>
						<option value="">All Locations</option>
						{locations.map( ( item, i ) => <option key={`location-${i}`}>{item}</option> )}
					</select>
				</div>

				<div className="select">
					<select name="type" value={type} onChange={self.onFilterChange}>
						<option>All Types</option>
						<option value="events">Events</option>
						<option value="podcast">Podcasts</option>
						<option value="news">News</option>
						<option value="video">Video</option>
						<option value="contests">Contests</option>
					</select>
				</div>

				<div className="select">
					<select name="genre" value={genre} onChange={self.onFilterChange}>
						<option value="">All Genres</option>
						{genres.map( ( item, i ) => <option key={`genre-${i}`}>{item}</option> )}
					</select>
				</div>

				<form role="search" className="search-form" onSubmit={self.onSubmit}>
					<label htmlFor="search-q" id="q" className="screen-reader-text">Search for:</label>
					<input id="search-q" type="search" className="search-field" name="s" value={keyword} placeholder="Search" onChange={self.onKeywordChange} />
					<button type="submit" className="search-submit" aria-label="Submit search">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="15">
							<path d="M10.266 9.034h-.65l-.23-.222a5.338 5.338 0 0 0 1.216-4.385C10.216 2.144 8.312.32 6.012.042a5.342 5.342 0 0 0-5.97 5.97c.279 2.3 2.102 4.204 4.385 4.59a5.338 5.338 0 0 0 4.385-1.215l.222.23v.649l3.49 3.49c.337.336.887.336 1.224 0s.336-.887 0-1.224l-3.482-3.498zm-4.928 0c-2.044 0-3.695-1.65-3.695-3.696s1.65-3.695 3.695-3.695 3.696 1.65 3.696 3.695-1.65 3.696-3.696 3.696z" fill="currentcolor" />
						</svg>
					</button>
				</form>
			</div>
		);
		/* eslint-enable */
	}
}

DiscoveryFilters.propTypes = {
	onChange: PropTypes.func.isRequired,
};

export default DiscoveryFilters;
