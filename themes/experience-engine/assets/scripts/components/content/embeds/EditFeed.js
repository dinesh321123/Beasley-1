import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { showEditFeedModal } from '../../../redux/actions/modal';

function EditFeed( { loggedIn, showModal, feed, title, feeds, className } ) {
	const hasFeed = feeds.find( item => item.id === feed );
	if ( !hasFeed ) {
		return false;
	}

	return loggedIn 
		? <button className={className} onClick={() => showModal( { feed, title } )} aria-label={`Edit ${title} Feed`}>Edit</button>
		: false;
}

EditFeed.propTypes = {
	loggedIn: PropTypes.bool.isRequired,
	feed: PropTypes.string.isRequired,
	feeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	title: PropTypes.string,
	className: PropTypes.string,
	showModal: PropTypes.func.isRequired,
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
		showModal: showEditFeedModal,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( EditFeed );
