/**
 * Polyfill for closest.
 *
 * Provides support for IE9+, in our case, IE11 needs this functionality.
 */

( function() {
	if ( ! Element.prototype.matches ) {
		Element.prototype.matches = Element.prototype.msMatchesSelector ||
			Element.prototype.webkitMatchesSelector;
	}

	if ( ! Element.prototype.closest ) {
		Element.prototype.closest = function( s ) {
			var el = this;

			do {
				if ( el.matches( s ) ) return el;
				el = el.parentElement || el.parentNode;
			}
			while ( null !== el && 1 === el.nodeType );
			return null;
		};
	}
} )( );
