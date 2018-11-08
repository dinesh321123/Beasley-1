let embedsIndex = 0;

const getSecondStreetEmbedParams = ( element ) => {
	const { dataset } = element;

	return {
		script: element.getAttribute( 'src' ),
		embed: dataset.ssEmbed,
		opguid: dataset.opguid,
		routing: dataset.routing,
	};
};

const getAudioEmbedParams = ( element ) => {
	const sources = {};
	const tags = element.getElementsByTagName( 'source' );
	for ( let i  = 0, len = tags.length; i < len; i++ ) {
		sources[tags[i].getAttribute( 'src' )] = tags[i].getAttribute( 'type' );
	}

	return {
		src: element.getAttribute( 'src' ) || '',
		sources,
	};
};

const getOmnyEmbedParams = ( element ) => {
	const { dataset } = element;

	return {
		src: element.getAttribute( 'src' ),
		title: dataset.title,
		author: dataset.author,
		omny: true,
	};
};

const getLazyImageParams = ( element ) => {
	const { dataset } = element;

	return {
		src: dataset.src,
		width: dataset.width,
		height: dataset.height,
		aspect: dataset.aspect,
	};
};

const getLoadMoreParams = ( element ) => ( {
	link: element.getAttribute( 'href' ),
} );

const processEmbeds = ( container, type, selector, callback ) => {
	const embeds = [];

	const elements = container.querySelectorAll( selector );
	for ( let i = 0, len = elements.length; i < len; i++ ) {
		const element = elements[i];
		const extraAttributes = callback ? callback( element ) : {};
		const placeholder = document.createElement( 'div' );

		placeholder.setAttribute( 'id', `__cd-${++embedsIndex}` );
		placeholder.classList.add( 'placeholder' );
		placeholder.classList.add( `placeholder-${type}` );

		embeds.push( {
			type,
			params: {
				placeholder: placeholder.getAttribute( 'id' ),
				...extraAttributes,
			},
		} );

		element.parentNode.replaceChild( placeholder, element );
	}

	return embeds;
};

export const getStateFromContent = ( container ) => {
	const state = {
		embeds: [],
		content: '',
	};

	if ( container ) {
		state.embeds = [
			...processEmbeds( container, 'secondstreet', '.secondstreet-embed', getSecondStreetEmbedParams ),
			...processEmbeds( container, 'audio', '.wp-audio-shortcode', getAudioEmbedParams ),
			...processEmbeds( container, 'audio', '.omny-embed', getOmnyEmbedParams ),
			...processEmbeds( container, 'lazyimage', '.lazy-image', getLazyImageParams ),
			...processEmbeds( container, 'share', '.share-buttons' ),
			...processEmbeds( container, 'loadmore', '.load-more', getLoadMoreParams ),
		];

		// MUST follow after embeds processing
		state.content = container.innerHTML;
	}

	return state;
};

export const parseHtml = ( html ) => {
	const parser = new DOMParser();

	const pageDocument = parser.parseFromString( html, 'text/html' );
	const content = pageDocument.querySelector( '#content' );

	const state = getStateFromContent( content );
	state.document = pageDocument;

	return state;
};

export default {
	getStateFromContent,
	parseHtml,
};
