import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';
import Alert from './elements/Alert';
import CloseButton from './elements/Close';
import DiscoveryFilters from './discovery/Filters';

class Discover extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.scrollYPos = 0;
		self.state = {
			filters: '',
			error: '',
		};

		self.onFilterChange = self.handleFilterChange.bind( self );
	}

	componentDidMount() {
		const self = this;

		self.props.activateTrap();
		self.scrollYPos = window.pageYOffset;

		window.scroll( 0, 0 );
	}

	componentWillUnmount() {
		const self = this;

		self.props.deactivateTrap();

		window.scroll( 0, self.scrollYPos );
	}

	handleFilterChange( filters ) {
		// @todo: pull feeds based on filters
	}

	render() {
		const self = this;
		const { error } = self.state;
		const { close } = self.props;

		return (
			<Fragment>
				<CloseButton close={close} />
				<DiscoveryFilters onChange={self.onFilterChange} />

				<Header>
					<h2>Discover</h2>
				</Header>

				<Alert message={error} />
			</Fragment>
		);
	}

}

Discover.propTypes = {
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
};

export default trapHOC()( Discover );
