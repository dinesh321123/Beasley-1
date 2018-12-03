import React, { Fragment } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import DelayedComponent from './DelayedEmbed';
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
};

function ContentBlock( { content, embeds, partial } ) {
	const portal = ReactDOM.createPortal(
		<div dangerouslySetInnerHTML={{ __html: content }} />,
		document.getElementById( partial ? 'inner-content' : 'content' )
	);

	const embedComponents = embeds.map( ( embed ) => {
		const { type, params } = embed;
		const { placeholder } = params;

		let component = mapping[type] || false;
		if ( component ) {
			component = React.createElement( component, params );
			component = React.createElement( DelayedComponent, { key: placeholder, placeholder }, component );
		}

		return component;
	} );

	return (
		<Fragment>
			{portal}
			{embedComponents}
		</Fragment>
	);
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
