import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';
import Alert from './elements/Alert';
import CloseButton from './elements/Close';

import FeedItem from './discovery/Feed';
import DiscoveryFilters from './discovery/Filters';

import { discovery } from '../../library/experience-engine';

class Discover extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.scrollYPos = 0;
		self.state = {
			error: '',
			feeds: [],
		};

		self.onFilterChange = self.handleFilterChange.bind( self );
	}

	componentDidMount() {
		const self = this;

		self.props.activateTrap();
		self.handleFilterChange();

		self.scrollYPos = window.pageYOffset;
		window.scroll( 0, 0 );
	}

	componentWillUnmount() {
		const self = this;

		self.props.deactivateTrap();
		window.scroll( 0, self.scrollYPos );
	}

	handleFilterChange( filters = {} ) {
		const self = this;
		const { token } = self.props;

		discovery( window.bbgiconfig.publisher.id, token, filters )
			.then( response => response.json() )
			.then( feeds => self.setState( { feeds } ) );
	}

	render() {
		const self = this;
		const { error, feeds } = self.state;
		const { close } = self.props;

		const items = feeds.map( item => (
			<FeedItem key={item.id} id={item.id} title={item.title} picture={item.picture} type={item.type}>
				{item.title}
			</FeedItem>
		) );

		return (
			<Fragment>
				<CloseButton close={close} />
				<DiscoveryFilters onChange={self.onFilterChange} />

				<div className="content-wrap">
					<Header>
						<h2>Discover</h2>
					</Header>

					<Alert message={error} />

					<div className="archive-tiles -small -grid">
						{items}
					</div>
				</div>
			</Fragment>
		);
	}

}

Discover.propTypes = {
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
	token: PropTypes.string.isRequired,
};

function mapStateToProps( { auth } ) {
	return {
		token: auth.token,
	};
}

export default connect( mapStateToProps )( trapHOC()( Discover ) );
