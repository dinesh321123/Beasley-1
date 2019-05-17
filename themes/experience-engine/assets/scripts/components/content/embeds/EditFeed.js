import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { modifyUserFeeds, deleteUserFeed } from '../../../redux/actions/auth';

class EditFeed extends Component {

	constructor( props ) {
		super( props );

		const self = this;
		self.move = self.move.bind( self );
		self.onRemove = self.handleRemove.bind( self );
		self.onMoveUp = self.handleMoveUp.bind( self );
		self.onMoveDown = self.handleMoveDown.bind( self );
	}

	handleMoveDown() {
		this.move( 'down' );
	}

	handleMoveUp() {
		this.move( 'up' );
	}

	move( direction ) {
		console.log( `move ${direction}` );
	}

	handleRemove() {
		const { feed, deleteFeed } = this.props;

		deleteFeed( feed );

		const container = document.getElementById( feed );
		if ( container ) {
			container.classList.add( '-hidden' );
		}
	}

	render() {
		const self = this;
		const { loggedIn, className } = self.props;

		if ( ! loggedIn ) {
			return false;
		}

		return (
			<div className="edit-feed-controls">
				<button className={className} aria-label="Move Down Feed" onClick={self.onMoveUp}>
					<svg width="14" height="9" aria-labelledby="move-down-modal-title move-down-modal-desc"  xmlns="http://www.w3.org/2000/svg">
						<title id="move-down-modal-title">Move Down</title>
						<path d="M12.88 2.275L7.276 7.88a.38.38 0 0 1-.552 0L1.12 2.275a.38.38 0 0 1 0-.554l.601-.6a.38.38 0 0 1 .554 0L7 5.846l4.726-4.727a.38.38 0 0 1 .553 0l.601.601a.38.38 0 0 1 0 .554z" fill="currentColor" stroke="currentColor" strokeWidth=".5"/>
					</svg>
				</button>

				<button className={className} aria-label="Move Up Feed" onClick={self.onMoveUp}>
					<svg width="14" height="9" aria-labelledby="move-up-modal-title move-up-modal-desc"  xmlns="http://www.w3.org/2000/svg">
						<title id="move-up-modal-title">Move Up</title>
						<path d="M1.12 6.725L6.724 1.12a.38.38 0 0 1 .552 0l5.604 5.605a.38.38 0 0 1 0 .554l-.601.6a.38.38 0 0 1-.553 0L7 3.154 2.274 7.88a.38.38 0 0 1-.553 0l-.601-.601a.38.38 0 0 1 0-.554z" fill="currentColor" stroke="currentColor" strokeWidth=".5"/>
					</svg>
				</button>

				<button className={className} aria-label="Remove Feed" onClick={self.onRemove}>
					<svg width="13" height="14" aria-labelledby="close-modal-title close-modal-desc" fill="none" xmlns="http://www.w3.org/2000/svg">
						<title id="close-modal-title">Close</title>
						<path fillRule="evenodd" clipRule="evenodd" d="M6.707 7.707L11 12l.707-.707L7.414 7l4.293-4.293L11 2 6.707 6.293l-5-5L1 2l5 5-5 5 .707.707 5-5z" fill="currentColor"/>
						<path d="M11 12l-.354.354.354.353.354-.353L11 12zM6.707 7.707l.354-.353L6.707 7l-.353.354.353.353zm5 3.586l.354.354.353-.354-.353-.354-.354.354zM7.414 7l-.353-.354L6.707 7l.354.354L7.414 7zm4.293-4.293l.354.354.353-.354-.353-.353-.354.353zM11 2l.354-.354L11 1.293l-.354.353L11 2zM6.707 6.293l-.353.353.353.354.354-.354-.354-.353zm-5-5l.354-.354-.354-.353-.353.353.353.354zM1 2l-.354-.354L.293 2l.353.354L1 2zm5 5l.354.354L6.707 7l-.353-.354L6 7zm-5 5l-.354-.354L.293 12l.353.354L1 12zm.707.707l-.353.354.353.353.354-.353-.354-.354zm9.647-1.06L7.06 7.353l-.707.707 4.292 4.293.708-.707zm0-.708l-.708.707.708.708.707-.707-.707-.708zM7.06 7.354l4.293 4.292.707-.707-4.293-4.293-.707.708zm4.293-5L7.06 6.646l.707.708L12.06 3.06l-.707-.707zm-.708 0l.708.707.707-.707-.707-.708-.708.708zM7.061 6.646l4.293-4.292-.708-.708L6.354 5.94l.707.707zm-5.707-5l5 5 .707-.707-5-5-.707.707zm0 .708l.707-.708L1.354.94l-.708.707.708.708zm5 4.292l-5-5-.708.708 5 5 .708-.708zm-5 5.708l5-5-.708-.708-5 5 .708.708zm.707 0l-.707-.707-.708.707.708.707.707-.707zm4.293-5l-5 5 .707.707 5-5-.707-.707z" fill="currentColor"/>
					</svg>
				</button>
			</div>
		);
	}
}

EditFeed.propTypes = {
	loggedIn: PropTypes.bool.isRequired,
	feed: PropTypes.string.isRequired,
	feeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	title: PropTypes.string,
	className: PropTypes.string,
	modifyFeeds: PropTypes.func.isRequired,
	deleteFeed: PropTypes.func.isRequired,
};

EditFeed.defaultProps = {
	title: '',
	className: '',
};

function mapStateToProps( { auth } ) {
	return {
		loggedIn: !!auth.user,
		feeds: auth.feeds,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		modifyFeeds: modifyUserFeeds,
		deleteFeed: deleteUserFeed,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( EditFeed );
