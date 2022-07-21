# Experience Engine Theme

The Experience Engine theme is a hybrid WordPress/React theme that powers the majority of the Beasley sites. Legacy themes are still used on an as-needed basis, use the Experience Engine theme as the default for new sites.

## Building the theme

In order to build the theme, install all dependencies first:

```
npm install
```

There are a few commands you can use to compile the theme's assets

1. Building for production
```
npm run bundle
```
2. Building for development
```
npm run build
```
3. Building and watching for changes
```
npm run watch
```

The assets are compiled down to the `bundle` folder and the JavaScript is enqueued by the WordPress functions `wp_register_script` and `wp_enqueue_script` in `include/assets.php`.

```php
function ee_enqueue_front_scripts() {
	// ... more code ...
	$base = untrailingslashit( get_template_directory_uri() );
	$min = $is_script_debug ? '' : '.min';

	wp_enqueue_style( 'ee-app', "{$base}/bundle/main.css", null, GREATERMEDIA_VERSION );

	// ... more code ...
	wp_enqueue_script( 'ee-app', "{$base}/bundle/app.js", $deps, GREATERMEDIA_VERSION, true );
	wp_add_inline_script( 'ee-app', $bbgiconfig, 'before' );
}
```
See the full code of the [ee_enqueue_front_scripts](includes/assets.php) for more details.

## Git Hooks and Linting

This theme uses [eslint-config-airbnb](https://github.com/airbnb/javascript) with a few modifications for linting all the JavaScript code.

There are two commands you can use to manually lint and format all the JavaScript code.

```
npm run lint:js
```

and

```
npm run format:js
```

Husky and lint-staged is also set up to run `eslint` on every commit, which means all code must pass the eslint config before being pushed to the repo.

It's possible to bypass these checks but it's discouraged and highly recommended to check with the project technical lead before doing so.

```
git commit -m "New feature" --no-verify
```

## The Hybrid approach

The Experience Engine theme uses a "hybrid" approach making use of [React Portals](https://reactjs.org/docs/portals.html). The tl;dr is that WordPress is responsible for bootstraping and generating each page, each page may contains a couple of "embeds" or "placeholders" elements that are read and parsed by React and re-injected in the page through React Portals. The purpose of this 'hybrid' approach is to be able to reuse legacy site features without needing to completely rewrite all functionality.

### Embeds/React Components

When an user visits any page, WordPress render the page as usual, following the [WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/), based on the page, WordPress might expose `embeds` such as the Related Posts one:

```php
echo sprintf(
	'<div class="related-articles content-wrap"
		  data-postid="%d"
		  data-posttitle="%s"
		  data-posttype="%s"
		  data-categories="%s"
		  data-url="%s"></div>',
	get_the_ID(),
	get_the_title(),
	get_post_type(),
	implode( ',', $categories ),
	get_the_permalink()
);
```

Click [here](partials/related-articles.php) to view the full code of this embed.

The `embed` must pass all the props needed. The `data-*` attributes will be exposed as props to the corresponding React component:

```js
import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';

const RelatedPosts = ({ posttype, posttitle, categories, url }) => {
	// ... more code ...
	return (
		<div className="related-articles content-wrap">
			<h2 className="section-head">
				<span>You Might Also Like</span>
			</h2>

			<div className="archive-tiles -list">
				{relatedPosts.filter(deduplicate).map(relatedPost => (
					<RelatedPost
						key={relatedPost.id}
						id={relatedPost.id}
						url={relatedPost.url}
						title={relatedPost.title}
						primary_image={relatedPost.primary_image}
						published={relatedPost.published}
					/>
				))}
			</div>
		</div>
	);
};
```
Take a look at the [RelatedPosts.js](assets/scripts/components/content/embeds/RelatedPosts.js) to see the full component.

All the embeds must have a corresponding React component, and they live inside the `assets/scripts/component/content/embeds` directory.

### Processing embeds
Embeds are parsed by React and mapped to the corresponding React components by the [parseHtml](assets/scripts/library/html-parser.js) function. New embed services need to be added to this logic to be rendered as expected. The parseHtml function parses the full html of the page and calls `getStateFromContent` which makes many calls to `processEmbeds`. Here's an example of how that works:

```js
export function getStateFromContent(container) {
	const state = {
		scripts: {},
		embeds: [],
		content: '',
	};

	if (container) {
		state.embeds = [
			// ... more code ...
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
			// ... more code ...
		];

		// ... more code ...
	}

	return state;
}
```

Note that it is looking inside the `container` (which by default is `#content`) for any `.related-articles` elements on the page and is creating an embed called `relatedposts`. It is also grabbing the `data-*` attributes in the `.related-articles` placeholder using the  `getDatasetParams` helper function. This is later being dispatched to the redux store so that the [ContentBlock.js](assets/scripts/components/content/ContentBlock.js) component can render the embeds.

### Rendering embeds

The [ContentBlock.js](assets/scripts/components/content/ContentBlock.js) component is responsible for reading the avaliable embeds from the Redux Store and rendering them on the page.

```js
// ... more code ...
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

// ... more code ...
	render() {
		const { content, embeds, partial, isHome } = this.props;
		const { ready } = this.state;

		const portal = ReactDOM.createPortal(
			<div dangerouslySetInnerHTML={{ __html: content }} />,
			document.getElementById(partial ? 'inner-content' : 'content'),
		);

		const embedComponents = ready
			? embeds.map(ContentBlock.createEmbed)
			: false;

		// The Homepage component exposes a few methods via the context api to allow editing feeds.
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
```

Note the `mapping` object, it is what maps the identifier of the embed in the Redux Store to an actual React component. When rendering, the ContentBlock component re-renders the whole content plus the embeds.

## How React loads a page

To better understands how everything fits together, let's take a look at how React loads a new page. The [ContentDispatcher.js](assets/scripts/modules/ContentDispatcher.js) component is the one responsbible for initializing the first-load page and also subsequent pages.

### Initial Load

When `ContentDispatcher` mounts it calls the `initPage` action creator. This action simply calls `getStateFromContent` (on first load there's no need to parse the html) and dispatches the embeds to the redux store:

```js
// assets/scripts/redux/actions/screen.js
/**
 * Parses the current content blocks for redux.
 */
export function initPage() {
	const content = document.getElementById('content');
	const parsed = getStateFromContent(content);

	// clean up content block for now, it will be poplated in the render function
	removeChildren(content);

	return {
		type: ACTION_INIT_PAGE,
		payload: {
			content: parsed.content,
			embeds: parsed.embeds,
			scripts: parsed.scripts,
		},
	};
}
```

It then dispatches a `ACTION_INIT_PAGE` which triggers a Redux Saga side effect [yieldInitPage.js](assets/scripts/redux/saga/screen/yieldInitPage.js)

```js
/**
 * Generator runs whenever [ ACTION_INIT_PAGE ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 * @param { Object } action.scripts Scripts from action
 */
function* yieldInitPage(action) {
	const { scripts } = action.payload;

	// Screen store from state.
	const screenStore = yield select(({ screen }) => screen);

	// load/update any scripts

	// ... more code ...

	yield put({ type: ACTION_SET_SCREEN_STATE, payload: action.payload });

	// ... more code ...

	// update history
}
```

The init page side effects does a few things but the most important operation is dispatching the screen state. With the screen state in Redux the `ContentBlock` component is able to render all embeds to the page.

### Loading subsequent pages (as in a SPA)

For the theme work like a SPA, it handles clicks to any intenral links very differently. The [ContentDispatcher.js](assets/scripts/modules/ContentDispatcher.js) component listens for any clicks on the page and intercepts them.

```js
	/**
	 * Handle the click links and if it's an internal links trigger the
	 * page loading process.
	 *
	 * If the user is logged in and the click is for the homepage, the feed will be fetched
	 * from Experience Engine by calling fetchFeeedsContent.
	 *
	 * @see assets/js/redux/actions/screen.js
	 *
	 * @param {event} e The event object.
	 */
	handleClick(e) {
		const { fetchPage, fetchFeedsContent } = this.props;

		const { target } = e;
		let linkNode = target;

		// ... more code

		// do nothing if this link has to be opened in a new window
		if (linkNode.getAttribute('target') === '_blank') {
			return;
		}

		// ... more code ...

		// target link is internal page, thus stop propagation and prevent default actions
		e.preventDefault();
		e.stopPropagation();

		if ( /* user is logged in and navigating to homepage */ ) {
			firebaseAuth.currentUser
				.getIdToken()
				.then(token => {
					// we don't want to supressHistory here as we want to update the URL to the homepage.
					fetchFeedsContent(token, link, { supressHistory: false });
				})
				.catch(() => {
					// fallback to loading regular homepage if fetchFeedsContent fails.
					fetchPage(link);
				});
		} else {
			// if it's a regular internal page (not homepage) just fetch the page as usual.
			fetchPage(link);
		}
	}
```

When the user clicks on any internal links, `ContentDispatcher` does a few checks and simply calls the `fetchPage` action creator. Alternatively, if the user is logged in and is navigating to the homepage the `fetchFeedsContent` action creator is called to load the personalized user feeds.

### fetchPage
The `fetchPage` action creator calls the [Page](../../mu-plugins/classes/Bbgi/Endpoints/Page.php) endpoint and receives back a list of potential redirects and the raw HTML of the page to be loaded.

```js
/**
 * Fetches a page by calling the page endpoint.
 *
 * @param {string} url
 * @param {object} Options
 */
export const fetchPage = (url, options = {}) => async dispatch => {
	const pageEndpoint = `${
		window.bbgiconfig.wpapi
	}\page?url=${encodeURIComponent(url)}`; // eslint-disable-line no-useless-escape

	try {
		dispatch({ type: ACTION_LOADING_PAGE, url });

		const response = await fetch(pageEndpoint).then(response =>
			response.json(),
		);

		// handle redirects
		// handle errors
		const parsedHtml = parseHtml(response.html);

		dispatch({
			type: ACTION_LOADED_PAGE,
			url,
			response,
			options,
			isHome: parsedHtml.document.body.classList.contains('home'),
			parsedHtml,
		});
	} catch (error) {
		dispatch({ type: ACTION_LOAD_ERROR, error });
	}
};
```
Note that `fetchPage` dispatches the response (parseHtml) to `ACTION_LOADED_PAGE` which in turns triggers a saga called [yieldLoadedPage.js](assets/scripts/redux/sagas/screen/yieldLoadedPage.js) which does several side effects operations.

### fetchFeedsContent
The `fetchFeedsContent` action creator calls the [feeds-content](../../mu-plugins/classes/Bbgi/Integration/ExperienceEngine.php) endpoint (see the `rest_get_feeds_content` method) and receives back the raw HTML of the page to be loaded. The Firebase user token is sent so that the server can fetch the user feeds.

```js
/**
 * Fetches the feed content for a user.
 *
 * @param {string} token Firease ID token
 * @param {string} url   Optional URL to associate the feeds content to.
 * @param {object} Options
 */
export const fetchFeedsContent = (
	token,
	url = '',
	options = { suppressHistory: true },
) => async dispatch => {
	dispatch({ type: ACTION_LOADING_PAGE, url });

	try {
		const response = await fetch(
			`${window.bbgiconfig.wpapi}feeds-content?device=other`,
			{
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: `authorization=${encodeURIComponent(token)}`,
			},
		).then(res => res.json());

		const parsedHtml = parseHtml(response.html);
		dispatch({
			type: ACTION_LOADED_PAGE,
			url,
			response,
			options,
			isHome: true,
			parsedHtml,
		});
	} catch (error) {
		dispatch({ type: ACTION_LOAD_ERROR, error });
	}
};
```
Similarly to `fetchPage` it dispatches and trigger the `yieldLoadedPage` saga.

# Redux

Go to the [Redux docs](assets/scripts/redux/README.md) for Redux specific documentation.

# General Guidelines

- Use React Hooks and functional components instead of classes. Several components were already converted to functional components with hooks. We recommend any new component to be created this way. E.g:
	- [App.js](../app.js)
	- [RelatedPosts.js](../components/content/embeds/RelatedPosts.js)
	- [EditFeed.js](../components/content/embeds/EditFeed.js)
- When making changes or introducing new funtionality, make sure to keep this README.md up to date.
