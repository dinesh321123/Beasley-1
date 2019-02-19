import { removeElement } from './dom';

let embedsIndex = 0;

function getSecondStreetEmbedParams( element ) {
	const { dataset } = element;

	return {
		script: element.getAttribute( 'src' ),
		embed: dataset.ssEmbed,
		opguid: dataset.opguid,
		routing: dataset.routing,
	};
}

function getAudioEmbedParams( element ) {
	const sources = {};
	const tags = element.getElementsByTagName( 'source' );
	for ( let i  = 0, len = tags.length; i < len; i++ ) {
		sources[tags[i].getAttribute( 'src' )] = tags[i].getAttribute( 'type' );
	}

	return {
		src: element.getAttribute( 'src' ) || '',
		sources,
	};
}

function getOmnyEmbedParams( element ) {
	const { dataset } = element;

	return {
		src: element.getAttribute( 'src' ),
		title: dataset.title,
		author: dataset.author,
		omny: true,
	};
}

function getDatasetParams( ...list ) {
	return ( { dataset } ) => {
		const params = {};
		for ( let i = 0, len = list.length; i < len; i++ ) {
			params[list[i]] = dataset[list[i]];
		}

		return params;
	};
}

function getLoadMoreParams( element ) {
	return {
		link: element.getAttribute( 'href' ),
	};
}

function getLiveStreamVideoParams( element ) {
	const attrs = { adTagUrl: element.dataset.adTag };

	const video = element.getElementsByTagName( 'video' )[0];
	if ( video ) {
		attrs.id = video.getAttribute( 'id' );
		attrs.poster = video.getAttribute( 'poster' );
		attrs.src = video.dataset.src;
	}

	return attrs;
}

function getDfpParams( { dataset } ) {
	const { targeting } = dataset;

	let keyvalues = [];

	try {
		if ( 'string' === typeof targeting && targeting ) {
			keyvalues = JSON.parse( targeting );
		} else if ( Array.isArray( targeting ) ) {
			keyvalues = targeting;
		}
	} catch ( err ) {
		// do nothing
	}

	return {
		unitId: dataset.unitId,
		unitName: dataset.unitName,
		targeting: keyvalues,
	};
}

function getPayloadParams( flattern = false ) {
	return ( { dataset } ) => {
		const { payload } = dataset;
		const params = {};

		try {
			if ( 'string' === typeof payload && payload ) {
				params.payload = JSON.parse( payload );
			} else if ( 'object' === typeof payload ) {
				params.payload = payload;
			}
		} catch( err ) {
			// do nothing
		}

		return flattern ? params.payload : params;
	};
}

function processEmbeds( container, type, selector, callback ) {
	const embeds = [];

	const elements = container.querySelectorAll( selector );
	for ( let i = 0, len = elements.length; i < len; i++ ) {
		const element = elements[i];
		const extraAttributes = callback ? callback( element ) : {};
		const placeholder = document.createElement( 'div' );

		placeholder.setAttribute( 'id', extraAttributes.id || `__cd-${++embedsIndex}` );
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
}

export function getStateFromContent( container ) {
	const state = {
		scripts: {},
		embeds: [],
		content: '',
	};

	if ( container ) {
		state.embeds = [
			...processEmbeds( container, 'dfp', '.dfp-slot', getDfpParams ),
			...processEmbeds( container, 'secondstreet', '.secondstreet-embed', getSecondStreetEmbedParams ),
			...processEmbeds( container, 'audio', '.wp-audio-shortcode', getAudioEmbedParams ),
			...processEmbeds( container, 'audio', '.lazy-audio', getDatasetParams( 'src', 'title', 'author' ) ),
			...processEmbeds( container, 'audio', '.omny-embed', getOmnyEmbedParams ),
			...processEmbeds( container, 'lazyimage', '.lazy-image', getDatasetParams( 'src', 'width', 'height', 'alt', 'tracking' ) ),
			...processEmbeds( container, 'share', '.share-buttons', getDatasetParams( 'url', 'title' ) ),
			...processEmbeds( container, 'loadmore', '.load-more', getLoadMoreParams ),
			...processEmbeds( container, 'video', '.livestream', getLiveStreamVideoParams ),
			...processEmbeds( container, 'embedvideo', '.youtube', getDatasetParams( 'title', 'thumbnail', 'html' ) ),
			...processEmbeds( container, 'cta', '.cta', getPayloadParams() ),
			...processEmbeds( container, 'countdown', '.countdown', getPayloadParams() ),
			...processEmbeds( container, 'streamcta', '.stream-cta', getPayloadParams( true ) ),
			...processEmbeds( container, 'discovery', '.discovery-cta', getPayloadParams() ),
			...processEmbeds( container, 'favorites', '.add-to-favorites', getDatasetParams( 'keyword' ) ),
			...processEmbeds( container, 'editfeed', '.edit-feed', getDatasetParams( 'feed', 'title' ) ),
		];

		// extract <script> tags
		const scripts = container.getElementsByTagName( 'script' );
		for ( let i = 0, len = scripts.length; i < len; i++ ) {
			const element = scripts[i];
			if ( element.src ) {
				state.scripts[element.src] = element.outerHTML;
			}
		}

		while ( scripts.length ) {
			removeElement( scripts[0] );
		}

		// MUST follow after embeds processing
		state.content = container.innerHTML;
	}

	return state;
}

export function parseHtml( html, selector = '#content' ) {
	const parser = new DOMParser();

	const pageDocument = parser.parseFromString( html, 'text/html' );
	const content = pageDocument.querySelector( selector );

	const state = getStateFromContent( content );
	state.document = pageDocument;

	return state;
}

export default {
	getStateFromContent,
	parseHtml,
};
