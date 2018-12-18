import React, { PureComponent } from 'react';

class BackToTop extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { show: false };

		self.onBackToTop = self.handleBackToTop.bind( self );
		self.onScroll = self.handleScroll.bind( self );
	}

	componentDidMount() {
		window.addEventListener( 'scroll', this.onScroll );
	}

	componentWillUnmount() {
		window.removeEventListener( 'scroll', this.onScroll );
	}

	handleScroll() {
		const self = this;
		const { show } = self.state;

		window.requestAnimationFrame( () => {
			const scrolly = window.scrollY || window.pageYOffset;
			if ( scrolly <= window.innerHeight ) {
				if ( show ) {
					self.setState( { show: false } );
				}
			} else if ( ! show ) {
				self.setState( { show: true } );
			}
		} );
	}

	handleBackToTop() {
		window.scrollTo( 0, 0 );
	}

	render() {
		const self = this;
		const { show } = self.state;

		if ( !show ) {
			return false;
		}

		return (
			<button className="back-to-top" aria-label="Back to top" onClick={self.onBackToTop}>
				Back to top
			</button>
		);
	}

}

export default BackToTop;
