import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import trapHOC from '@10up/react-focus-trap-hoc';

import { firebaseAuth } from '../../library/firebase';
import Header from './elements/Header';
import Alert from './elements/Alert';
import CloseButton from './elements/Close';
import Notification from '../Notification';

import FeedItem from './discovery/Feed';
import DiscoveryFilters from './discovery/Filters';

import { discovery } from '../../library/experience-engine';

import { modifyUserFeeds, deleteUserFeed } from '../../redux/actions/auth';
import { fetchFeedsContent } from '../../redux/actions/screen';

class Discover extends Component {
	constructor(props) {
		super(props);

		this.needReload = false;
		this.scrollYPos = 0;

		this.state = {
			loading: true,
			error: '',
			pageNum: 1,
			pageSize: 20,
			filteredFeeds: [],
			pendingPageNum: 0,
			pendingScrollX: 0,
			pendingScrollY: 0,
		};

		this.onFilterChange = this.handleFilterChange.bind(this);
		this.onAdd = this.handleAdd.bind(this);
		this.onRemove = this.handleRemove.bind(this);
		this.onClose = this.handleClose.bind(this);
		this.didLoadMoreClick = this.didLoadMoreClick.bind(this);
	}

	componentDidMount() {
		this.props.activateTrap();
		this.handleFilterChange();

		this.scrollYPos = window.pageYOffset;
		window.scroll(0, 0);
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
		window.scroll(0, this.scrollYPos);
	}

	handleFilterChange(filters = {}) {
		discovery(filters)
			.then(response => response.json())
			.then(feeds => {
				this.setState({
					pageNum: 1,
					filteredFeeds: feeds,
					loading: false,
				});
			});
	}

	hasFeed(id) {
		return !!this.props.selectedFeeds.find(item => item.id === id);
	}

	handleAdd(id) {
		const feedsArray = [];

		if (this.hasFeed(id)) {
			return;
		}

		this.props.selectedFeeds.forEach(({ id }) => {
			feedsArray.push({ id, sortorder: feedsArray.length + 1 });
		});

		feedsArray.push({ id, sortorder: feedsArray.length + 1 });

		this.needReload = true;
		this.props.modifyUserFeeds(feedsArray);
	}

	handleRemove(id) {
		if (this.hasFeed(id)) {
			this.needReload = true;
			this.props.deleteUserFeed(id);
		}
	}

	handleClose() {
		if (this.needReload && document.body.classList.contains('home')) {
			firebaseAuth.currentUser.getIdToken().then(token => {
				this.props.fetchFeedsContent(token);
			});
		}

		this.props.close();
	}

	/**
	 * Increment page number and store previous scroll position for later
	 * update
	 */
	didLoadMoreClick() {
		this.setState(({ pageNum }) => ({
			pageNum: pageNum + 1,
			pendingPageNum: pageNum + 1,
			pendingScrollX: window.scrollX,
			pendingScrollY: window.scrollY,
		}));

		return false;
	}

	/**
	 * If a new page was rendered on Discovery we have to shift scroll
	 * offset to avoid jitter. We do this by resetting the scroll to value
	 * prior to loading the next page. The element check is necessary to ensure
	 * that atleast one element with the new pageNum was rendered.
	 */
	componentDidUpdate() {
		if (this.state.pendingPageNum) {
			const el = document.querySelector(
				`[data-pagenum="${this.state.pendingPageNum}"]`,
			);

			if (el) {
				window.scrollTo(this.state.pendingScrollX, this.state.pendingScrollY);

				// eslint-disable-next-line react/no-did-update-set-state
				this.setState({
					pendingPageNum: 0,
					pendingScrollX: 0,
					pendingScrollY: 0,
				});
			}
		}
	}

	render() {
		const { error, loading, pageNum, pageSize } = this.state;
		const { notice } = this.props;
		const noticeClass = !notice.isOpen ? '' : '-visible';

		let { filteredFeeds } = this.state;
		const totalPages = filteredFeeds.length / pageSize;
		const hasNextPage = pageNum < totalPages;

		if (filteredFeeds.length > 0) {
			filteredFeeds = filteredFeeds.slice(0, pageSize * pageNum);
		}

		let items = <div className="loading" />;
		if (!loading) {
			if (filteredFeeds.length > 0) {
				items = filteredFeeds.map((item, index) => {
					const { id, title, picture, type } = item;

					return (
						<FeedItem
							key={id}
							id={id}
							pageNum={Math.floor((index + 1) / pageSize) + 1}
							title={title}
							picture={picture}
							type={type}
							onAdd={this.onAdd}
							onRemove={this.onRemove}
							added={this.hasFeed(item.id)}
						/>
					);
				});
			} else {
				items = <i>No feeds found...</i>;
			}
		}

		return (
			<>
				<CloseButton close={this.onClose} />
				<DiscoveryFilters onChange={this.onFilterChange} />

				<div className="content-wrap">
					<Header>
						<h2>Discover</h2>
					</Header>

					<Notification message={notice.message} noticeClass={noticeClass} />

					<Alert message={error} />

					<div className="archive-tiles -small -grid">{items}</div>
				</div>

				{!loading && hasNextPage && (
					<div className="load-more-feeds">
						<button
							type="button"
							className="btn load-more-button"
							aria-label="Load More Feeds"
							onClick={this.didLoadMoreClick}
						>
							Load More
						</button>
					</div>
				)}
			</>
		);
	}
}

Discover.propTypes = {
	selectedFeeds: PropTypes.arrayOf(PropTypes.object).isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
	fetchFeedsContent: PropTypes.func.isRequired,
	modifyUserFeeds: PropTypes.func.isRequired,
	deleteUserFeed: PropTypes.func.isRequired,
	notice: PropTypes.shape({
		isOpen: PropTypes.bool,
		message: PropTypes.string,
	}).isRequired,
};

export default connect(
	({ auth, screen }) => ({
		selectedFeeds: auth.feeds,
		notice: screen.notice,
	}),
	{
		fetchFeedsContent,
		modifyUserFeeds,
		deleteUserFeed,
	},
)(trapHOC()(Discover));
