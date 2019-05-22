import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { searchKeywords } from '../../../library/experience-engine';
import { modifyUserFeeds, deleteUserFeed } from '../../../redux/actions/auth';
import { showSignInModal } from '../../../redux/actions/modal';

class AddToFavorites extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			hidden: !props.feedId,
			feed: props.feedId,
		};

		self.onAddClick = self.handleAddClick.bind( self );
		self.onRemoveClick = self.handleRemoveClick.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { keyword } = self.props;
		if ( !keyword ) {
			return;
		}

		searchKeywords( keyword )
			.then( ( feeds ) => {
				if ( Array.isArray( feeds ) && feeds.length ) {
					const newState = { hidden: false };
					for ( let i = 0, len = feeds.length; i < len; i++ ) {
						if ( feeds[i].id ) {
							newState.feed = feeds[i].id;
							break;
						}
					}

					self.setState( newState );
				}
			} )
			.catch( () => ( {} ) );
	}

	hasFeed() {
		const self = this;
		const { feed } = self.state;

		return !!self.props.selectedFeeds.find( item => item.id === feed );
	}

	handleAddClick() {
		const self = this;
		const feedsArray = [];
		const { signedIn, showSignIn } = self.props;

		if ( ! signedIn ) {
			showSignIn();
			return;
		}

		self.props.selectedFeeds.forEach( ( item ) => {
			feedsArray.push( { 
				id: item.id,
				sortorder: feedsArray.length + 1,
			} );
		} );

		feedsArray.push( { 
			id: self.state.feed,
			sortorder: feedsArray.length + 1,
		} );

		self.props.modifyUserFeeds( feedsArray );
	}

	handleRemoveClick() {
		const self = this;
		if ( self.hasFeed() ) {
			self.props.deleteUserFeed( self.state.feed );
		}
	}

	render() {
		const self = this;
		const { classes, addLabel, removeLabel, showIcon } = self.props;

		const { hidden } = self.state;
		if ( hidden ) {
			return false;
		}

		let icon = false;

		if ( self.hasFeed() ) {
			if ( showIcon ) {
				icon = (
					<svg width="15" height="15" xmlns="http://www.w3.org/2000/svg">
						<rect y="6.61502" x="0" id="svg_2" height="2" width="15"/>
					</svg>
				);
			}
			
			return (
				<button className={`btn ${classes}`} onClick={self.onRemoveClick}>
					{icon}
					{removeLabel}
				</button>
			);
		}

		if ( showIcon ) {
			icon = (
				<svg width="15" height="15" xmlns="http://www.w3.org/2000/svg">
					<path fillRule="evenodd" clipRule="evenodd" d="M8.5 0h-2v6.5H0v2h6.5V15h2V8.5H15v-2H8.5V0z"/>
				</svg>
			);
		}
		
		return (
			<button className={`btn ${classes}`} onClick={self.onAddClick}>
				{icon}
				{addLabel}
			</button>
		);
	}

}

AddToFavorites.propTypes = {
	selectedFeeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	feedId: PropTypes.string,
	keyword: PropTypes.string,
	classes: PropTypes.string,
	addLabel: PropTypes.string,
	removeLabel: PropTypes.string,
	signedIn: PropTypes.bool.isRequired,
	showIcon: PropTypes.bool,
	modifyUserFeeds: PropTypes.func.isRequired,
	deleteUserFeed: PropTypes.func.isRequired,
	showSignIn: PropTypes.func.isRequired,
};

AddToFavorites.defaultProps = {
	feedId: '',
	keyword: '',
	classes: '-empty -nobor -icon',
	addLabel: 'Add to my feed',
	removeLabel: 'Remove from my feed',
	showIcon: true,
};

function mapStateToProps( { auth } ) {
	return {
		signedIn: !!auth.user,
		selectedFeeds: auth.feeds,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		modifyUserFeeds,
		deleteUserFeed,
		showSignIn: showSignInModal,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( AddToFavorites );
