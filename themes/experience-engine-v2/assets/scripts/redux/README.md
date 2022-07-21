# Experience Engine Redux Documentation

The Experience Engine theme uses Redux for global state management. The following libraries are used alongside Redux:

- [react-redux](https://github.com/reduxjs/react-redux) for Redux integration with React.
- [redux-thunk](https://github.com/reduxjs/redux-thunk) for allowing to dispatch async actions.
- [redux-sagas](https://redux-saga.js.org/) for handling side effects.


# Folder Structure

All the related redux files lives inside the `assets/scripts/redux` directory. This folder is broken down as follows:

- `actions/` Contains the definitions of all actions creators, action creators are grouped on a file related to its functionality (e.g: `player.js`)
- `reducers/` Contains all the reducers, grouped on a file related to its functionality.
- `sagas/` Contains all the side effects handling grouped by folder.
	- `player/` Sagas related to the player.
	- `screen/` Sagas related to page fetching logic.
	- `index.js` Exports all sagas.
- `utilities/` Contain several utilities functions used when mutating state or perfoming side effects.
- `sagas.js` The root saga.
- `store.js` Creates the global redux store

# General Rules
When writing Redux-related code please keep in mind the following rules:

- Always create a constant to represent the name of you action and always reference that action through this constant.
```js
export const ACTION_LOADED_PAGE = 'ACTION_LOADED_PAGE'
```
- Reducer should be pure functions. Don't ever make modifications to the DOM or any other side effects (such as hooking up on player events). If you need to perform a side effect as a result of a dispatched action, use a `saga` for that.
- Action creators should also generally not be used to handle side effects to keep things simple, altought it's fine to perform *minor side effects* where creating a dedicated saga might be too much.
- Prefer creating an action creator for dispatching more complex actions. Generally speaking, components should *always* use action creators instead of dispatching an object directly.
```js
	componentDidMount() {
		const { initPage, dispatch } = this.props;

		// good!
		initPage();
		// bad!
		dispatch({type: ACTION_INIT_PAGE})
	}
```
- Prefer using React Hooks for interfacing with the Redux store. React-Redux already supports [several hooks](https://react-redux.js.org/api/hooks). A few components were refactored to use React Hooks already, but this migration is not complete. We recommend using Redux Hooks when writing any new components that needs to talk to the Redux store.

# Notable Action Creators
Here's a list of the most important action creators.

- `initPage` - Initializes the first-load page in Redux store.
- `fetchPage` - Fetch a new page, parses the HTML and dispatches the ebmed to redux.
- `fetchFeedsContent` - Fetches the user homepage feeds. Only used when loading the homepage for logge din users.
- `playStation` - Used to start the live streaming
- `playAudio` - Used to play any audio file (typically mp3)
- `playOmny` - Used to play omnny audio files (rarely used).
- `play` - A lower-level action creater used by the other the other play actions. In components always use `playStation`, `playAudio` or `playOmny`

# Redux Sagas

We use Redux Sagas for handling side effects. You can think of Redux Sagas as events, i.e, anytime an action is dispatched (event) you can respond via a saga (event callback).

For example, when a new page is fetched, `fetchPage` triggers `ACTION_LOADED_PAGE` and the `yieldLoadedPage.js` saga is triggered, performing several side effects.

```js
function* yieldLoadedPage(action) {
	// we have access to dispatched action
	const { url, response, options, parsedHtml } = action;

	// performs several side effects when a new page is loaded.

	// you can dispatch other actions, grab the state or cause other side effects.
}

export default function* watchLoadedPage() {
	yield takeLatest([ACTION_LOADED_PAGE], yieldLoadedPage);
}
```

Every saga exports a "watch" function, you can see that `watchLoadedPage` is a generator function and is "watching" for the latest `ACTION_LOADED_PAGE` and calling the `yieldLoadedPage` generator function once that action is dispatched.
