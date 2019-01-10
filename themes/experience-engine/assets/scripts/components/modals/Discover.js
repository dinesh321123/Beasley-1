import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';
import Alert from './elements/Alert';
import CloseButton from './elements/Close';

import FeedItem from './discovery/Feed';
import DiscoveryFilters from './discovery/Filters';

import { discovery, getFeeds, modifyFeeds, deleteFeed } from '../../library/experience-engine';

class Discover extends Component {

	constructor( props ) {
		super( props );

		const self = this;

		self.scrollYPos = 0;

		self.state = {
			loading: true,
			error: '',
			filteredFeeds: [],
			selectedFeeds: {},
		};

		self.onFilterChange = self.handleFilterChange.bind( self );
		self.onAdd = self.handleAdd.bind( self );
		self.onRemove = self.handleRemove.bind( self );
	}

	componentDidMount() {
		const self = this;

		getFeeds().then( ( items ) => {
			const selectedFeeds = {};

			items.forEach( ( item ) => {
				selectedFeeds[item.id] = item.id;
			} );

			self.setState( { selectedFeeds } );
		} );

		self.props.activateTrap();
		self.handleFilterChange();

		self.scrollYPos = window.pageYOffset;
		window.scroll( 0, 0 );
	}

	componentWillUnmount() {
		const self = this;

		self.props.deactivateTrap();
		window.scroll( 0, self.scrollYPos );
	}

	handleFilterChange( filters = {} ) {
		discovery( filters )
			.then( response => response.json() )
			.then( feeds => {
				this.setState( {
					filteredFeeds: feeds,
					loading: false,
				} );
			} );
	}

	handleAdd( id ) {
		const self = this;
		const selectedFeeds = { ...self.state.selectedFeeds };

		if ( selectedFeeds[id] ) {
			return;
		}

		selectedFeeds[id] = id;

		const feedsArray = [];
		Object.keys( selectedFeeds ).forEach( ( feed ) => {
			feedsArray.push( {
				id: feed,
				sortorder: feedsArray.length + 1,
			} );
		} );

		modifyFeeds( feedsArray ).then( () => {
			this.setState( { selectedFeeds } );
		} );
	}

	handleRemove( id ) {
		const self = this;
		const selectedFeeds = { ...self.state.selectedFeeds };

		if ( !selectedFeeds[id] ) {
			return;
		}

		delete selectedFeeds[id];

		deleteFeed( id ).then( () => {
			this.setState( { selectedFeeds } );
		} );
	}

	render() {
		const self = this;
		const { error, filteredFeeds, selectedFeeds, loading } = self.state;
		const { close } = self.props;

		let items = <div className="loading" />;
		if ( !loading ) {
			if ( 0 < filteredFeeds.length ) {
				items = filteredFeeds.map( ( item ) => {
					const { id, title, picture, type } = item;
					return <FeedItem key={id} id={id} title={title} picture={picture} type={type} onAdd={self.onAdd} onRemove={self.onRemove} added={!!selectedFeeds[item.id]} />;
				} );
			} else {
				items = <i>No feeds found...</i>;
			}
		}

		return (
			<Fragment>
				<CloseButton close={close} />
				<DiscoveryFilters onChange={self.onFilterChange} />

				<div className="content-wrap">
					<Header>
						<h2>Discover</h2>
					</Header>

					<Alert message={error} />

					<div className="archive-tiles -small -grid">
						{items}
					</div>
				</div>
			</Fragment>
		);
	}

}

Discover.propTypes = {
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
};

export default trapHOC()( Discover );
