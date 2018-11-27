import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { loadPartialPage } from '../../../redux/actions/screen';

class LoadMore extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { loading: false };
		self.onLoadClick = self.handleLoadClick.bind( self );
	}

	handleLoadClick() {
		const self = this;
		const { loading } = self.state;
		const { link, placeholder, load } = self.props;

		// prevent double clicking
		if ( !loading ) {
			self.setState( { loading: true } );
			load( link, placeholder );
		}
	}

	render() {
		const self = this;
		const { loading } = self.state;
		const { partialKeys, placeholder } = self.props;
		if ( -1 < partialKeys.indexOf( placeholder ) ) {
			return false;
		}

		const label = loading ? <div className="loading" /> : 'Load More';

		return (
			<div className="load-more-wrapper content-wrap">
				<button className="load-more" onClick={self.onLoadClick}>
					{label}
				</button>
			</div>
		);
	}

}

LoadMore.propTypes = {
	placeholder: PropTypes.string.isRequired,
	link: PropTypes.string.isRequired,
	partialKeys: PropTypes.arrayOf( PropTypes.string ).isRequired,
	load: PropTypes.func.isRequired,
};

const mapStateToProps = ( { screen } ) => ( {
	partialKeys: Object.keys( screen.partials ),
} );

const mapDispatchToProps = ( dispatch ) => bindActionCreators( {
	load: loadPartialPage,
}, dispatch );

export default connect( mapStateToProps, mapDispatchToProps )( LoadMore );
