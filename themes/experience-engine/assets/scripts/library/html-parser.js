import { removeElement } from './dom';

let embedsIndex = 0;

function getSecondStreetEmbedParams(element) {
	const { dataset } = element;

	return {
		script: element.getAttribute('src'),
		embed: dataset.ssEmbed,
		opguid: dataset.opguid,
		routing: dataset.routing,
	};
}

function getSecondStreetPrefEmbedParams(element) {
	const { dataset } = element;

	return {
		orgid: dataset.orgid,
	};
}

function getSecondStreetSignupEmbedParams(element) {
	const { dataset } = element;

	return {
		designid: dataset.designid,
	};
}

function getAudioEmbedParams(element) {
	const sources = {};
	const tags = element.getElementsByTagName('source');
	for (let i = 0, len = tags.length; i < len; i++) {
		sources[tags[i].getAttribute('src')] = tags[i].getAttribute('type');
	}

	return {
		src: element.getAttribute('src') || '',
		sources,
		title: element.dataset.title,
		author: element.dataset.author,
		tracktype: element.dataset.tracktype || '',
	};
}

function getOmnyEmbedParams(element) {
	const { dataset } = element;

	return {
		src: element.getAttribute('src'),
		title: dataset.title,
		author: dataset.author,
		omny: true,
		tracktype: dataset.tracktype,
	};
}

function getDatasetParams(...list) {
	return ({ dataset }) => {
		const params = {};
		for (let i = 0, len = list.length; i < len; i++) {
			params[list[i]] = dataset[list[i]];
		}

		return params;
	};
}

function getLoadMoreParams(element) {
	return {
		link: element.getAttribute('href'),
	};
}

function getEmbedlyParams(element) {
	const embedlyTitleElement = element.children[0];
	const embedlyTitleAElement = embedlyTitleElement.children[0];
	const embedlyParagraphElement =
		typeof element.children[1] !== 'undefined' ? element.children[1] : '';

	return {
		url: embedlyTitleAElement.href,
		title: embedlyTitleElement.textContent,
		description: embedlyParagraphElement.innerText,
	};
}

function getInstagramParams(element) {
	return {
		content: element.innerHTML,
	};
}

/**
 * Extracts the attributes passed by the Song Archive template
 * placeholder and returns them.
 *
 * @param element The song archive pre-render DOM element
 * @return object
 */
function getSongArchiveParams(element) {
	const { dataset } = element;

	return {
		callsign: dataset.callsign,
		endpoint: dataset.endpoint,
		description: dataset.description,
	};
}

function getDfpParams({ dataset }) {
	const { targeting } = dataset;

	let keyvalues = [];

	try {
		if (typeof targeting === 'string' && targeting) {
			keyvalues = JSON.parse(targeting);
		} else if (Array.isArray(targeting)) {
			keyvalues = targeting;
		}
	} catch (err) {
		// do nothing
	}

	return {
		unitId: dataset.unitId,
		unitName: dataset.unitName,
		targeting: keyvalues,
	};
}

function getPayloadParams(flattern = false) {
	return ({ dataset }) => {
		const { payload } = dataset;
		const params = {};

		try {
			if (typeof payload === 'string' && payload) {
				params.payload = JSON.parse(payload);
			} else if (typeof payload === 'object') {
				params.payload = payload;
			}
		} catch (err) {
			// do nothing
		}

		return flattern ? params.payload : params;
	};
}

function getMapboxParams(element) {
	const { dataset } = element;

	return {
		accesstoken: dataset.accesstoken,
		style: dataset.style,
		long: dataset.long,
		lat: dataset.lat,
		zoom: dataset.zoom,
	};
}

function processEmbeds(container, type, selector, callback) {
	const embeds = [];

	const elements = container.querySelectorAll(selector);

	for (let i = 0, len = elements.length; i < len; i++) {
		const element = elements[i];
		const extraAttributes = callback ? callback(element) : {};
		const placeholder = document.createElement('div');

		if (type === 'audio' && element.closest('.description')) {
			continue;
		}

		placeholder.setAttribute(
			'id',
			extraAttributes.id || `__cd-${++embedsIndex}`,
		);
		placeholder.classList.add('placeholder');
		placeholder.classList.add(`placeholder-${type}`);

		embeds.push({
			type,
			params: {
				placeholder: placeholder.getAttribute('id'),
				...extraAttributes,
			},
		});

		element.parentNode.replaceChild(placeholder, element);
	}

	return embeds;
}

export function getStateFromContent(container) {
	const state = {
		scripts: {},
		embeds: [],
		content: '',
	};

	if (container) {
		state.embeds = [
			...processEmbeds(container, 'dfp', '.dfp-slot', getDfpParams),
			...processEmbeds(
				container,
				'secondstreet',
				'.secondstreet-embed',
				getSecondStreetEmbedParams,
			),
			...processEmbeds(
				container,
				'secondstreetprefcenter',
				'.secondstreet-prefcenter',
				getSecondStreetPrefEmbedParams,
			),
			...processEmbeds(
				container,
				'secondstreetsignup',
				'.secondstreet-signup',
				getSecondStreetSignupEmbedParams,
			),
			...processEmbeds(
				container,
				'audio',
				'.wp-audio-shortcode',
				getAudioEmbedParams,
			),
			...processEmbeds(
				container,
				'audio',
				'.lazy-audio',
				getDatasetParams('src', 'title', 'author', 'tracktype'),
			),
			...processEmbeds(container, 'audio', '.omny-embed', getOmnyEmbedParams),
			...processEmbeds(
				container,
				'lazyimage',
				'.lazy-image',
				getDatasetParams(
					'src',
					'width',
					'height',
					'alt',
					'tracking',
					'attribution',
					'autoheight',
				),
			),
			...processEmbeds(
				container,
				'share',
				'.share-buttons',
				getDatasetParams('url', 'title'),
			),
			...processEmbeds(container, 'loadmore', '.load-more', getLoadMoreParams),
			...processEmbeds(
				container,
				'livestreamvideo',
				'.livestream',
				getDatasetParams('embedid', 'src'),
			),
			...processEmbeds(
				container,
				'embedvideo',
				'.youtube',
				getDatasetParams('title', 'thumbnail', 'html'),
			),
			...processEmbeds(container, 'cta', '.cta', getPayloadParams()),
			...processEmbeds(
				container,
				'countdown',
				'.countdown',
				getPayloadParams(),
			),
			...processEmbeds(
				container,
				'streamcta',
				'.stream-cta',
				getPayloadParams(true),
			),
			...processEmbeds(
				container,
				'discovery',
				'.discovery-cta',
				getPayloadParams(),
			),
			...processEmbeds(
				container,
				'favorites',
				'.add-to-favorites',
				getDatasetParams('keyword'),
			),
			...processEmbeds(
				container,
				'editfeed',
				'.edit-feed',
				getDatasetParams('feed', 'title'),
			),
			...processEmbeds(
				container,
				'embedly',
				'.embedly-card-prerender',
				getEmbedlyParams,
			),
			...processEmbeds(
				container,
				'instagram',
				'.instagram.responsive-media',
				getInstagramParams,
			),
			...processEmbeds(
				container,
				'songarchive',
				'.song-archive-prerender',
				getSongArchiveParams,
			),
			...processEmbeds(
				container,
				'relatedposts',
				'.related-articles',
				getDatasetParams(
					'postid',
					'categories',
					'posttype',
					'posttitle',
					'url',
				),
			),
			...processEmbeds(
				container,
				'ga',
				'.ga-info',
				getDatasetParams(
					'title',
					'url',
					'contentgroup1',
					'contentgroup2',
					'dimensionkey',
					'dimensionvalue',
				),
			),
			...processEmbeds(container, 'mapbox', '.mapbox', getMapboxParams),
		];

		// extract <script> tags
		const scripts = container.getElementsByTagName('script');
		for (let i = 0, len = scripts.length; i < len; i++) {
			const element = scripts[i];
			if (element.src) {
				state.scripts[element.src] = element.outerHTML;
			}
		}

		while (scripts.length) {
			removeElement(scripts[0]);
		}

		// MUST follow after embeds processing
		state.content = container.innerHTML;
	}

	return state;
}

export function parseHtml(html, selector = '#content') {
	const parser = new DOMParser();

	const pageDocument = parser.parseFromString(html, 'text/html');
	const content = pageDocument.querySelector(selector);

	const state = getStateFromContent(content);
	state.document = pageDocument;

	return state;
}

export default {
	getStateFromContent,
	parseHtml,
};
