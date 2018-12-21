import React from 'react';

const context = React.createContext();

export class Observable {

	constructor() {
		const self = this;
		const params = {
			rootMargin: '50px 0px',
			threshold: 0.01,
		};

		self.entries = new Map();
		self.observer = new IntersectionObserver( self.handleIntersection.bind( self ), params );
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

export default context;
