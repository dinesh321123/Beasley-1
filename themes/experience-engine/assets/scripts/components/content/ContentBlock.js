import React, { Fragment, Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import ErrorBoundary from '../ErrorBoundary';
import Homepage from './Homepage';

import AudioEmbed from './embeds/Audio';
import SecondStreetEmbed from './embeds/SecondStreet';
import LazyImage from './embeds/LazyImage';
import Share from './embeds/Share';
import LoadMore from './embeds/LoadMore';
import LivestreamVideo from './embeds/LivestreamVideo';
import EmbedVideo from './embeds/EmbedVideo';
import Dfp from './embeds/Dfp';
import Cta from './embeds/Cta';
import Countdown from './embeds/Countdown';
import StreamCta from './embeds/StreamCta';
import Discovery from './embeds/Discovery';
import AddToFavorites from './embeds/AddToFavorites';
import EditFeed from './embeds/EditFeed';
import Embedly from './embeds/Embedly';
import SongArchive from './embeds/SongArchive';
import RelatedPosts from './embeds/RelatedPosts';
import GoogleAnalytics from './embeds/GoogleAnalytics';

const mapping = {
	audio: AudioEmbed,
	countdown: Countdown,
	cta: Cta,
	dfp: Dfp,
	discovery: Discovery,
	editfeed: EditFeed,
	embedly: Embedly,
	embedvideo: EmbedVideo,
	favorites: AddToFavorites,
	lazyimage: LazyImage,
	livestreamvideo: LivestreamVideo,
	loadmore: LoadMore,
	secondstreet: SecondStreetEmbed,
	share: Share,
	songarchive: SongArchive,
	streamcta: StreamCta,
	relatedposts: RelatedPosts,
	ga: GoogleAnalytics,
};

class ContentBlock extends Component {
	static createEmbed(embed) {
		const { type, params } = embed;
		const { placeholder } = params;

		const component = mapping[type] || false;
		if (!component) {
			return false;
		}

		const container = document.getElementById(placeholder);
		if (!container) {
			return false;
		}

		const element = React.createElement(component, params);

		return ReactDOM.createPortal(
			React.createElement(ErrorBoundary, {}, element),
			container,
		);
	}

	constructor(props) {
		super(props);

		const self = this;
		self.state = { ready: false };
	}

	componentDidMount() {
		this.setState({ ready: true });
		this.bindContests();
	}

	/**
	 * Bind contest event listeners
	 */
	bindContests() {
		const contestToggler = document.getElementById('contest-rules-toggle');

		if (contestToggler) {
			contestToggler.addEventListener('click', this.handleContestClick);
		}
	}

	/**
	 * Handle 'view contest' link click
	 */
	handleContestClick(e) {
		const contestRules = document.getElementById('contest-rules');

		e.target.style.display = 'none';

		if (contestRules) {
			contestRules.style.display = 'block';
		}
	}

	render() {
		const self = this;
		const { content, embeds, partial, isHome } = self.props;
		const { ready } = self.state;

		const portal = ReactDOM.createPortal(
			<div dangerouslySetInnerHTML={{ __html: content }} />,
			document.getElementById(partial ? 'inner-content' : 'content'),
		);

		const embedComponents = ready
			? embeds.map(ContentBlock.createEmbed)
			: false;

		return isHome ? (
			<Homepage>
				{portal}
				{embedComponents}
			</Homepage>
		) : (
			<>
				{portal}
				{embedComponents}
			</>
		);
	}
}

ContentBlock.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf(PropTypes.object).isRequired,
	partial: PropTypes.bool,
	isHome: PropTypes.bool,
};

ContentBlock.defaultProps = {
	partial: false,
	isHome: false,
};

export default ContentBlock;
