import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import firebase from 'firebase';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';
import Alert from './elements/Alert';
import CloseButton from './elements/Close';

import FeedItem from './discovery/Feed';
import DiscoveryFilters from './discovery/Filters';

import { discovery } from '../../library/experience-engine';

import { modifyUserFeeds, deleteUserFeed } from '../../redux/actions/auth';
import { loadPage } from '../../redux/actions/screen';

class Discover extends Component {

	constructor( props ) {
		super( props );

		const self = this;

		self.needReload = false;
		self.scrollYPos = 0;

		self.state = {
			loading: true,
			error: '',
			filteredFeeds: [],
		};

		self.onFilterChange = self.handleFilterChange.bind( self );
		self.onAdd = self.handleAdd.bind( self );
		self.onRemove = self.handleRemove.bind( self );
		self.onClose = self.handleClose.bind( self );
	}

	componentDidMount() {
		const self = this;

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
		const selectedFeeds = { ...self.props.selectedFeeds };

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

		self.needReload = true;
		self.props.modifyUserFeeds( feedsArray );
	}

	handleRemove( id ) {
		const self = this;
		const selectedFeeds = { ...self.props.selectedFeeds };

		if ( !selectedFeeds[id] ) {
			return;
		}

		self.needReload = true;
		self.props.deleteUserFeed( id );
	}

	handleClose() {
		const self = this;

		if ( self.needReload && document.body.classList.contains( 'home' ) ) {
			const auth = firebase.auth();

			auth.currentUser.getIdToken().then( ( token ) => {
				self.props.loadPage( `${window.bbgiconfig.wpapi}feeds-content`, {
					suppressHistory: true,
					fetchParams: {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: `format=raw&authorization=${encodeURIComponent( token )}`,
					},
				} );
			} );
		}

		self.props.close();
	}

	render() {
		const self = this;
		const { error, filteredFeeds, loading } = self.state;
		const { selectedFeeds } = self.props;

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
				<CloseButton close={self.onClose} />
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
	selectedFeeds: PropTypes.shape( {} ).isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
	loadPage: PropTypes.func.isRequired,
	modifyUserFeeds: PropTypes.func.isRequired,
	deleteUserFeed: PropTypes.func.isRequired,
};

function mapStateToProps( { auth } ) {
	const selectedFeeds = {};
	auth.feeds.forEach( item => {
		selectedFeeds[item.id] = item.id;
	} );

	return { selectedFeeds };
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		loadPage,
		modifyUserFeeds,
		deleteUserFeed,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( trapHOC()( Discover ) );
