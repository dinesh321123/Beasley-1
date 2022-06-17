import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { IntersectionObserverContext } from '../../../context/intersection-observer';
import { pageview } from '../../../library/google-analytics';

class TrackLocation extends PureComponent {
	constructor(props) {
		super(props);
		this.onIntersectionChange = this.handleIntersectionChange.bind(this);
	}

	componentDidMount() {
		console.log('track location called');
		const { placeholder } = this.props;
		this.container = document.getElementById(placeholder);
		this.context.observe(this.container, this.onIntersectionChange);
	}

	componentWillUnmount() {
		this.context.unobserve(this.container);
	}

	handleIntersectionChange() {
		let changeAuthor = null;
		const { tracking, referrer, author, pageview_data } = this.props;

		// disable intersection observing
		this.context.unobserve(this.container);

		if (author && Object.keys(pageview_data).length > 0) {
			const dimensionKey = pageview_data?.dimensionkey;
			if (dimensionKey) {
				changeAuthor = {
					dimensionkey: dimensionKey,
					dimensionvalue: author,
				};
			}
		}

		// track virtual page view if it's needed
		if (tracking) {
			if (changeAuthor == null) {
				pageview(document.title, tracking);
			} else {
				pageview(document.title, tracking, null, changeAuthor);
			}

			if (window.PARSELY) {
				// eslint-disable-next-line no-undef
				PARSELY.beacon.trackPageView({
					url: tracking,
					urlref: referrer,
				});
			}
		}
	}

	render() {
		return false;
	}
}

TrackLocation.propTypes = {
	placeholder: PropTypes.string.isRequired,
	tracking: PropTypes.string,
	referrer: PropTypes.string,
	author: PropTypes.string,
	pageview_data: PropTypes.shape({}).isRequired,
};

TrackLocation.defaultProps = {
	tracking: '',
	referrer: '',
	author: '',
};

TrackLocation.contextType = IntersectionObserverContext;

function mapStateToProps({ ga }) {
	return {
		pageview_data: ga.pageview_data,
	};
}

export default connect(mapStateToProps, null)(TrackLocation);
