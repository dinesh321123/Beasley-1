import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { loadPage } from '../redux/actions/screen';

class SearchForm extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.onSearchSubmit = self.handleSearchSubmit.bind( self );
	}

	componentDidMount() {
		window.addEventListener( 'submit', this.onSearchSubmit );
	}

	componentWillUnmount() {
		window.removeEventListener( 'submit', this.onSearchSubmit );
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

const mapDispatchToProps = ( dispatch ) => bindActionCreators( { loadPage }, dispatch );

export default connect( null, mapDispatchToProps )( SearchForm );
