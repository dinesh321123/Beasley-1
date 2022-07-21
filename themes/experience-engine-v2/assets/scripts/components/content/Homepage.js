import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { HomepageOrderingContext } from '../../context';
import { modifyUserFeeds } from '../../redux/actions/auth';

class Homepage extends Component {
	static sortFeeds(a, b) {
		if (a.sortorder > b.sortorder) {
			return 1;
		}

		if (a.sortorder < b.sortorder) {
			return -1;
		}

		return 0;
	}

	constructor(props) {
		super(props);

		this.childrenContext = {
			moveUp: this.reorderFeeds.bind(this, 15),
			moveDown: this.reorderFeeds.bind(this, -15),
		};
	}

	componentDidMount() {
		this.updateOrderNumbers();
	}

	updateOrderNumbers() {
		this.repositionFeeds(this.shiftFeeds(0, false));
	}

	repositionFeeds(feeds) {
		let i = 0;
		feeds.forEach(item => {
			const child = document.querySelector(`#inner-content > #${item.id}`);
			if (child) {
				child.style.order = (i + 1) * 10;
				i++;
			}
		});

		// ie11
		let innerContent = document.querySelectorAll('#inner-content > div');
		innerContent = [].slice.call(innerContent);
		innerContent.forEach((child, i) => {
			if (child && !child.style.order) {
				child.style.order = i * 10 + 1;
			}
		});
	}

	shiftFeeds(shift, feed) {
		const { feeds } = this.props;

		let index = 0;
		let delta = 0;
		const newfeeds = feeds.map(item => {
			if (document.querySelector(`#inner-content > #${item.id}`)) {
				index++;
				delta = 0;
			} else {
				delta += 0.01;
			}

			return {
				id: item.id,
				sortorder: index * 10 + delta - (item.id === feed ? shift : 0),
			};
		});

		newfeeds.sort(Homepage.sortFeeds);
		for (let i = 0, len = newfeeds.length; i < len; i++) {
			newfeeds[i].sortorder = i + 1;
		}

		return newfeeds;
	}

	reorderFeeds(shift, feed) {
		const feeds = this.shiftFeeds(shift, feed);
		this.props.modifyFeeds(feeds);
		this.repositionFeeds(feeds);
	}

	render() {
		return (
			<HomepageOrderingContext.Provider value={this.childrenContext}>
				{this.props.children}
			</HomepageOrderingContext.Provider>
		);
	}
}

Homepage.propTypes = {
	children: PropTypes.node.isRequired,
	feeds: PropTypes.arrayOf(PropTypes.object).isRequired,
	modifyFeeds: PropTypes.func.isRequired,
};

function mapStateToProps({ auth }) {
	const feeds = auth.feeds.map(item => ({ ...item }));
	feeds.sort(Homepage.sortFeeds);

	return {
		feeds,
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			modifyFeeds: modifyUserFeeds,
		},
		dispatch,
	);
}

export default connect(mapStateToProps, mapDispatchToProps)(Homepage);
