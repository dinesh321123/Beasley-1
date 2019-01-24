import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { searchKeywords } from '../../../library/experience-engine';

class AddToFavorites extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			hidden: true,
		};

		self.onAddClick = self.handleAddClick.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { keyword } = self.props;
		if ( !keyword ) {
			return;
		}

		searchKeywords( keyword )
			.then( ( feeds ) => {
				self.setState( {
					hidden: ! Array.isArray( feeds ) || !feeds.length,
					feeds,
				} );
			} )
			.catch( () => ( {} ) );
	}

	handleAddClick() {
		console.log( 'adding', this.state.feeds );
	}

	render() {
		const self = this;

		const { hidden } = self.state;
		if ( hidden ) {
			return false;
		}

		return (
			<button className="btn -empty -nobor -icon" onClick={self.onAddClick}>
				<svg width="15" height="15" xmlns="http://www.w3.org/2000/svg">
					<path fillRule="evenodd" clipRule="evenodd" d="M8.5 0h-2v6.5H0v2h6.5V15h2V8.5H15v-2H8.5V0z"/>
				</svg>
				Add to my feed
			</button>
		);
	}

}

AddToFavorites.propTypes = {
	keyword: PropTypes.string,
};

AddToFavorites.defaultProps = {
	keyword: '',
};

export default AddToFavorites;
