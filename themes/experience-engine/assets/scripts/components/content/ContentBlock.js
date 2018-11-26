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
import Dfp from './embeds/Dfp';

const mapping = {
	secondstreet: SecondStreetEmbed,
	audio: AudioEmbed,
	lazyimage: LazyImage,
	share: Share,
	loadmore: LoadMore,
	video: Video,
	dfp: Dfp,
};

const ContentBlock = ( { content, embeds } ) => {
	const portal = ReactDOM.createPortal(
		<div dangerouslySetInnerHTML={{ __html: content }} />,
		document.getElementById( 'content' )
	);

	const embedComponents = embeds.map( ( embed ) => {
		const { type, params } = embed;
		const { placeholder } = params;

		let component = mapping[type] || false;
		if ( component ) {
			component = React.createElement( component, params );
			component = (
				<DelayedComponent key={placeholder} placeholder={placeholder}>
					{component}
				</DelayedComponent>
			);
		}

		return component;
	} );

	return (
		<Fragment>
			{portal}
			{embedComponents}
		</Fragment>
	);
};

ContentBlock.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
};

export default ContentBlock;
