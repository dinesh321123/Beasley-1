import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { searchKeywords } from '../../../library';
import { modifyUserFeeds, deleteUserFeed } from '../../../redux/actions/auth';
import { showSignInModal } from '../../../redux/actions/modal';

class AddToFavorites extends PureComponent {
	constructor(props) {
		super(props);

		this.state = {
			hidden: !props.feedId,
			feed: props.feedId,
		};

		this.onAddClick = this.handleAddClick.bind(this);
		this.onRemoveClick = this.handleRemoveClick.bind(this);
	}

	componentDidMount() {
		const { keyword } = this.props;
		if (!keyword) {
			return;
		}

		searchKeywords(keyword)
			.then(feeds => {
				if (Array.isArray(feeds) && feeds.length) {
					const newState = { hidden: false };
					for (let i = 0, len = feeds.length; i < len; i++) {
						if (feeds[i].id) {
							newState.feed = feeds[i].id;
							break;
						}
					}

					this.setState(newState);
				}
			})
			.catch(() => ({}));
	}

	hasFeed() {
		const { feed } = this.state;

		return !!this.props.selectedFeeds.find(item => item.id === feed);
	}

	handleAddClick() {
		const feedsArray = [];
		const { signedIn, showSignIn } = this.props;

		if (!signedIn) {
			showSignIn();
			return;
		}

		this.props.selectedFeeds.forEach(item => {
			feedsArray.push({
				id: item.id,
				sortorder: feedsArray.length + 1,
			});
		});

		feedsArray.push({
			id: this.state.feed,
			sortorder: feedsArray.length + 1,
		});

		this.props.modifyUserFeeds(feedsArray);
	}

	handleRemoveClick() {
		if (this.hasFeed()) {
			this.props.deleteUserFeed(this.state.feed);
		}
	}

	render() {
		const { classes, addLabel, removeLabel, showIcon } = this.props;

		const { hidden } = this.state;
		if (hidden) {
			return false;
		}

		let icon = false;

		if (this.hasFeed()) {
			if (showIcon) {
				icon = (
					<svg width="15" height="15" xmlns="http://www.w3.org/2000/svg">
						<rect y="6.61502" x="0" id="svg_2" height="2" width="15" />
					</svg>
				);
			}

			return (
				<button
					className={`btn ${classes}`}
					onClick={this.onRemoveClick}
					type="button"
				>
					{icon}
					{removeLabel}
				</button>
			);
		}

		if (showIcon) {
			icon = (
				<svg width="15" height="15" xmlns="http://www.w3.org/2000/svg">
					<path
						fillRule="evenodd"
						clipRule="evenodd"
						d="M8.5 0h-2v6.5H0v2h6.5V15h2V8.5H15v-2H8.5V0z"
					/>
				</svg>
			);
		}

		return (
			<button
				className={`btn ${classes}`}
				onClick={this.onAddClick}
				type="button"
			>
				{icon}
				{addLabel}
			</button>
		);
	}
}

AddToFavorites.propTypes = {
	selectedFeeds: PropTypes.arrayOf(PropTypes.object).isRequired,
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

function mapStateToProps({ auth }) {
	return {
		signedIn: !!auth.user,
		selectedFeeds: auth.feeds,
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			modifyUserFeeds,
			deleteUserFeed,
			showSignIn: showSignInModal,
		},
		dispatch,
	);
}

export default connect(mapStateToProps, mapDispatchToProps)(AddToFavorites);
