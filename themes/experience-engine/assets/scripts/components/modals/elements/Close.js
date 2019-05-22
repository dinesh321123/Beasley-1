import PropTypes from 'prop-types';
import React, { PureComponent, Fragment } from 'react';

class Close extends PureComponent {

	constructor( props ) {
		super( props );

		this.didClick = this.didClick.bind( this );
	}

	render() {
		return (
			<button type="button" className="button modal-close" aria-label="Close Modal" onClick={this.didClick}>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 212.982 212.982" aria-labelledby="close-modal-title close-modal-desc" width="13" height="13">
					<title id="close-modal-title">Close Modal</title>
					<desc id="close-modal-desc">
						Checkmark indicating modal close
					</desc>
					<path
						d="M131.804 106.491l75.936-75.936c6.99-6.99 6.99-18.323 0-25.312-6.99-6.99-18.322-6.99-25.312 0L106.491 81.18 30.554 5.242c-6.99-6.99-18.322-6.99-25.312 0-6.989 6.99-6.989 18.323 0 25.312l75.937 75.936-75.937 75.937c-6.989 6.99-6.989 18.323 0 25.312 6.99 6.99 18.322 6.99 25.312 0l75.937-75.937 75.937 75.937c6.989 6.99 18.322 6.99 25.312 0 6.99-6.99 6.99-18.322 0-25.312l-75.936-75.936z"
						fillRule="evenodd"
						clipRule="evenodd"
					/>
				</svg>
			</button>
		);
	}

	didClick() {
		const beforeClose = window.beforeBeasleyModalClose;
		const close = this.props.close;
		console.log( 'close', this.props );

		if ( beforeClose ) {
			const result = beforeClose();

			if ( result ) {
				if ( close ) {
					close();
				}
			}
		} else if ( close ) {
			close();
		}
	}

}

Close.propTypes = {
	close: PropTypes.func.isRequired,
};

export default Close;
