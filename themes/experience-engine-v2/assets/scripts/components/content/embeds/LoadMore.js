import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { loadPartialPage } from '../../../redux/actions/screen';

class LoadMore extends PureComponent {
	constructor(props) {
		super(props);

		this.state = { loading: false };
		this.onLoadClick = this.handleLoadClick.bind(this);
	}

	handleLoadClick() {
		const { loading } = this.state;
		const { link, placeholder, load } = this.props;

		// prevent double clicking
		if (!loading) {
			this.setState({ loading: true });
			load(link, placeholder);
		}
	}

	render() {
		const { loading } = this.state;
		const { partialKeys, placeholder } = this.props;
		if (partialKeys.indexOf(placeholder) > -1) {
			return false;
		}

		const label = loading ? <div className="loading" /> : 'Load More';

		return (
			<div className="load-more-wrapper">
				<button className="load-more" onClick={this.onLoadClick} type="button">
					{label}
				</button>
			</div>
		);
	}
}

LoadMore.propTypes = {
	placeholder: PropTypes.string.isRequired,
	link: PropTypes.string.isRequired,
	partialKeys: PropTypes.arrayOf(PropTypes.string).isRequired,
	load: PropTypes.func.isRequired,
};

const mapStateToProps = ({ screen }) => ({
	partialKeys: Object.keys(screen.partials),
});

const mapDispatchToProps = dispatch =>
	bindActionCreators(
		{
			load: loadPartialPage,
		},
		dispatch,
	);

export default connect(mapStateToProps, mapDispatchToProps)(LoadMore);
