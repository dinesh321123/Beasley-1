import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { loadPage } from '../redux/actions/screen';

class SearchForm extends PureComponent {

	constructor( props ) {
		super( props );
		this.onSearchSubmit = this.handleSearchSubmit.bind( this );
	}

	componentDidMount() {
		this.searchForm = document.querySelector( '.search-form' );

		if ( this.searchForm ) {
			this.searchForm.addEventListener( 'submit', this.onSearchSubmit );
		}

	}

	componentWillUnmount() {
		if ( this.searchForm ) {
			this.searchForm.removeEventListener( 'submit', this.onSearchSubmit );
		}
	}

	handleSearchSubmit( e ) {
		const { target } = e;

		e.preventDefault();

		const url = target.getAttribute( 'action' ) || '/';
		const formData = new FormData( target );
		const search = formData.get( 's' );
		if ( search && search.length ) {
			this.props.loadPage( `${url}?s=${encodeURIComponent( search )}` );
			target.querySelector( 'input[name="s"]' ).value = '';
		}
	}

	render() {
		return false;
	}

}

SearchForm.propTypes = {
	loadPage: PropTypes.func.isRequired,
};

export default connect( null, { loadPage } )( SearchForm );
