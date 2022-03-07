import React from 'react';

/**
 * The Homepage ordering context exposes two functions: moveUp and moveDown.
 *
 * These functions are exposed by the Homepage.js component.
 */
const HomepageOrderingContext = React.createContext({
	moveUp: () => {},
	moveDown: () => {},
});

export default HomepageOrderingContext;
