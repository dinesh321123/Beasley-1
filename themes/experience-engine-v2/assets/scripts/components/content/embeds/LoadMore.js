import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { loadPartialPage } from '../../../redux/actions/screen';
import { IntersectionObserverContext } from '../../../context/intersection-observer';
import updateTargeting from '../../../redux/utilities/screen/updateTargeting';

class LoadMore extends PureComponent {
	constructor(props) {
		super(props);
		let { showafter } = this.props;
		showafter = showafter ? Number(showafter) : 4;
		const loadmoreCount = document.getElementsByClassName(
			'placeholder-loadmore',
		);
		const showButton =
			loadmoreCount.length > 0 && loadmoreCount.length % (showafter + 1) === 0;
		this.state = { loading: false, button: showButton };
		this.onLoadClick = this.handleLoadClick.bind(this);
		this.onIntersectionChange = this.handleIntersectionChange.bind(this);
	}

	componentDidMount() {
		const { placeholder } = this.props;
		this.container = document.getElementById(placeholder);

		const autoloadOffset = document.getElementById('autoload-category-archive');
		if (autoloadOffset) {
			this.container = autoloadOffset;
		}
		this.context.observe(this.container, this.onIntersectionChange);
	}

	componentWillUnmount() {
		this.context.unobserve(this.container);
	}

	handleIntersectionChange() {
		const { autoload } = this.props;
		if (autoload) {
			const { button } = this.state;
			if (!button) {
				// Load more when button gets in view
				this.handleLoadClick();
			}
		}

		// disable intersection observing
		this.context.unobserve(this.container);
		if (autoload) {
			this.container.remove();
		}
	}

	handleLoadClick() {
		const { loading } = this.state;
		const { link, placeholder, load } = this.props;

		// prevent double clicking
		if (!loading) {
			this.setState({ loading: true });
			updateTargeting();
			load(link, placeholder);
		}
	}

	render() {
		const { loading, button } = this.state;
		const { partialKeys, placeholder } = this.props;
		if (partialKeys.indexOf(placeholder) > -1) {
			return false;
		}

		const { autoload } = this.props;
		if (autoload) {
			const buttonDiv = button ? (
				<div className="load-more-wrapper">
					<button
						className="load-more"
						onClick={this.onLoadClick}
						type="button"
					>
						Load More
					</button>
				</div>
			) : (
				<div />
			);
			return loading ? <div className="ca-autoload-loading" /> : buttonDiv;
		}

		const label = loading ? <div className="loading" /> : 'Load More';
		return (
			<div className="load-more-wrapper">
				<button className="load-more" onClick={this.onLoadClick} type="button">
					{label}
				</button>
			</div>
		);
	}
}

LoadMore.propTypes = {
	placeholder: PropTypes.string.isRequired,
	link: PropTypes.string.isRequired,
	partialKeys: PropTypes.arrayOf(PropTypes.string).isRequired,
	load: PropTypes.func.isRequired,
	autoload: PropTypes.string,
	showafter: PropTypes.string,
};

LoadMore.defaultProps = {
	autoload: '',
	showafter: '',
};

LoadMore.contextType = IntersectionObserverContext;

const mapStateToProps = ({ screen }) => ({
	partialKeys: Object.keys(screen.partials),
});

const mapDispatchToProps = dispatch =>
	bindActionCreators(
		{
			load: loadPartialPage,
		},
		dispatch,
	);

export default connect(mapStateToProps, mapDispatchToProps)(LoadMore);
