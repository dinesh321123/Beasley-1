import React from 'react';
import PropTypes from 'prop-types';

export const IntersectionObserverContext = React.createContext();

export class Observable {

	constructor() {
		const params = {
			rootMargin: '50px 0px',
			threshold: 0.01,
		};

		this.entries = new Map();
		this.observer = new IntersectionObserver( this.handleIntersection.bind( this ), params );
	}

	handleIntersection( entries ) {
		for ( let i = 0, len = entries.length; i < len; i++ ) {
			const entry = entries[i];
			if ( entry.isIntersecting ) {
				const callback = this.entries.get( entry.target );
				if ( 'function' === typeof callback ) {
					callback( entry );
				}
			}
		}
	}

	observe( target, callback ) {
		this.entries.set( target, callback );
		this.observer.observe( target );
	}

	unobserve( target ) {
		this.entries.delete( target );
		this.observer.unobserve( target );
	}

}

export const observer = new Observable();

/**
 * A provider components that makes the intersection obverser object avaliable to any child component.
 *
 * This intersection observer notifies whenever a elements gets into view.
 */
const IntersectionObserverProvider = ( {children} ) => {
	return (
		<IntersectionObserverContext.Provider value={observer}>
			{children}
		</IntersectionObserverContext.Provider>
	);
};

IntersectionObserverProvider.propTypes = {
	children: PropTypes.oneOfType( [
		PropTypes.arrayOf( PropTypes.node ),
		PropTypes.node,
	] ).isRequired,
};

export default IntersectionObserverProvider;
