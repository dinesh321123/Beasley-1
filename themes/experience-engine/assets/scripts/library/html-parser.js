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

function getLazyImageParams( { dataset } ) {
	return {
		src: dataset.src,
		width: dataset.width,
		height: dataset.height,
		alt: dataset.alt,
	};
}

function getShareParams( { dataset } ) {
	return {
		url: dataset.url,
		title: dataset.title,
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

function getEmbedVideoParams( { dataset } ) {
	return {
		title: dataset.title,
		thumbnail: dataset.thumbnail,
		html: dataset.html,
	};
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
		network: dataset.network,
		unitId: dataset.unitId,
		unitName: dataset.unitName,
		targeting: keyvalues,
	};
}

function processEmbeds( container, type, selector, callback ) {
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
			...processEmbeds( container, 'audio', '.omny-embed', getOmnyEmbedParams ),
			...processEmbeds( container, 'lazyimage', '.lazy-image', getLazyImageParams ),
			...processEmbeds( container, 'share', '.share-buttons', getShareParams ),
			...processEmbeds( container, 'loadmore', '.load-more', getLoadMoreParams ),
			...processEmbeds( container, 'video', '.livestream', getLiveStreamVideoParams ),
			...processEmbeds( container, 'embedvideo', '.youtube', getEmbedVideoParams ),
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
