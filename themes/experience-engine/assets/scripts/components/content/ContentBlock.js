import React, { Fragment, Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import AudioEmbed from './embeds/Audio';
import SecondStreetEmbed from './embeds/SecondStreet';
import LazyImage from './embeds/LazyImage';
import Share from './embeds/Share';
import LoadMore from './embeds/LoadMore';
import Video from './embeds/Video';
import EmbedVideo from './embeds/EmbedVideo';
import Dfp from './embeds/Dfp';
import Cta from './embeds/Cta';
import Countdown from './embeds/Countdown';
import StreamCta from './embeds/StreamCta';
import Discovery from './embeds/Discovery';
import AddToFavorites from './embeds/AddToFavorites';
import EditFeed from './embeds/EditFeed';

const mapping = {
	secondstreet: SecondStreetEmbed,
	audio: AudioEmbed,
	lazyimage: LazyImage,
	share: Share,
	loadmore: LoadMore,
	video: Video,
	embedvideo: EmbedVideo,
	dfp: Dfp,
	cta: Cta,
	countdown: Countdown,
	streamcta: StreamCta,
	discovery: Discovery,
	favorites: AddToFavorites,
	editfeed: EditFeed,
};

class ContentBlock extends Component {

	static createEmbed( embed ) {
		const { type, params } = embed;
		const { placeholder } = params;

		const component = mapping[type] || false;
		if ( !component ) {
			return false;
		}

		const container = document.getElementById( placeholder );
		if ( !container ) {
			return false;
		}

		return ReactDOM.createPortal(
			React.createElement( component, params ),
			container,
		);
	}

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { ready: false };
	}

	componentDidMount() {
		this.setState( { ready: true } );
	}

	render() {
		const self = this;
		const { content, embeds, partial } = self.props;
		const { ready } = self.state;

		const portal = ReactDOM.createPortal(
			<div dangerouslySetInnerHTML={{ __html: content }} />,
			document.getElementById( partial ? 'inner-content' : 'content' )
		);

		const embedComponents = ready ? embeds.map( ContentBlock.createEmbed ) : false;

		return (
			<Fragment>
				{portal}
				{embedComponents}
			</Fragment>
		);
	}

}

ContentBlock.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	partial: PropTypes.bool,
};

ContentBlock.defaultProps = {
	partial: false,
};

export default ContentBlock;
