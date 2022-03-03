import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';

import { modifyUserFeeds, deleteUserFeed } from '../../redux/actions/auth';

class EditFeed extends PureComponent {
	static sortFeeds(a, b) {
		if (a.sortorder > b.sortorder) {
			return 1;
		}

		if (a.sortorder < b.sortorder) {
			return -1;
		}

		return 0;
	}

	static getFeedsHash(feeds) {
		const feedsHash = {};

		for (let i = 0, len = feeds.length; i < len; i++) {
			feedsHash[feeds[i].id] = i + 1;
		}

		return feedsHash;
	}

	constructor(props) {
		super(props);

		this.onMoveToTopClick = this.handleMoveToTopClick.bind(this);
		this.onMoveUpClick = this.handleMoveUpClick.bind(this);
		this.onDeleteClick = this.handleDeleteClick.bind(this);
		this.onMoveDownClick = this.handleMoveDownClick.bind(this);
		this.onMoveToBottomClick = this.handleMoveToBottomClick.bind(this);
	}

	componentDidMount() {
		this.props.activateTrap();

		const feeds = this.shiftFeeds(0);
		const feedsHash = EditFeed.getFeedsHash(feeds);
		const container = document.getElementById('inner-content');
		if (container) {
			for (let i = 0, index = 0; i < container.childNodes.length; i++) {
				const child = container.childNodes[i];
				if (child && child.id) {
					if (feedsHash[child.id]) {
						child.style.order = (feedsHash[child.id] + 1) * 10;
						index = child.style.order;
					} else {
						child.style.order = index + 1;
					}
				}
			}
		}
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
	}

	shiftFeeds(shift) {
		const { feed, feeds } = this.props;

		const newfeeds = feeds.map((item, i) => ({
			id: item.id,
			sortorder: i * 10 - (item.id === feed ? shift : 0),
		}));

		newfeeds.sort(EditFeed.sortFeeds);
		for (let i = 0, len = newfeeds.length; i < len; i++) {
			newfeeds[i].sortorder = i + 1;
		}

		return newfeeds;
	}

	reorderFeeds(shift) {
		const feeds = this.shiftFeeds(shift);
		this.props.modifyFeeds(feeds);

		const feedsHash = EditFeed.getFeedsHash(feeds);
		const container = document.getElementById('inner-content');
		if (container) {
			for (
				let i = 0, keys = Object.keys(feedsHash), len = keys.length;
				i < len;
				i++
			) {
				const element = document.getElementById(keys[i]);
				if (element) {
					element.style.order = (feedsHash[keys[i]] + 1) * 10;
				}
			}
		}
	}

	handleMoveToTopClick() {
		this.reorderFeeds(1000000);
	}

	handleMoveUpClick() {
		this.reorderFeeds(15);
	}

	handleDeleteClick() {
		const { close, deleteFeed, feed } = this.props;

		deleteFeed(feed);

		const container = document.getElementById(feed);
		if (container) {
			container.classList.add('-hidden');
		}

		close();
	}

	handleMoveDownClick() {
		this.reorderFeeds(-15);
	}

	handleMoveToBottomClick() {
		this.reorderFeeds(-1000000);
	}

	render() {
		const { title, feed } = this.props;
		const label = title || feed || 'Feed';

		return (
			<>
				<Header>{label}</Header>

				<div>
					<button
						className="btn"
						onClick={this.onMoveToTopClick}
						aria-label={`Move ${label} To Top`}
						type="button"
					>
						Move To Top
					</button>
					<button
						className="btn"
						onClick={this.onMoveUpClick}
						aria-label={`Move Up ${label}`}
						type="button"
					>
						Move Up
					</button>
					<button
						className="btn"
						onClick={this.onMoveDownClick}
						aria-label={`Move Down ${label} To Top`}
						type="button"
					>
						Move Down
					</button>
					<button
						className="btn"
						onClick={this.onMoveToBottomClick}
						aria-label={`Move ${label} To Bottom`}
						type="button"
					>
						Move To Bottom
					</button>
				</div>

				<hr />

				<div>
					<button
						className="btn"
						onClick={this.onDeleteClick}
						aria-label={`Delete ${label}`}
						type="button"
					>
						Delete
					</button>
				</div>
			</>
		);
	}
}

EditFeed.propTypes = {
	feed: PropTypes.string.isRequired,
	feeds: PropTypes.arrayOf(PropTypes.object).isRequired,
	title: PropTypes.string,
	close: PropTypes.func.isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	deleteFeed: PropTypes.func.isRequired,
	modifyFeeds: PropTypes.func.isRequired,
};

EditFeed.defaultProps = {
	title: '',
};

function mapStateToProps({ auth }) {
	const items = auth.feeds.map(item => ({ ...item }));
	items.sort(EditFeed.sortFeeds);

	return { feeds: items };
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			deleteFeed: deleteUserFeed,
			modifyFeeds: modifyUserFeeds,
		},
		dispatch,
	);
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(trapHOC()(EditFeed));
