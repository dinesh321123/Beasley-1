import { Component } from 'react';
import PropTypes from 'prop-types';

class ErrorBoundary extends Component {
	constructor(props) {
		super(props);
		this.state = { error: false };
	}

	static getDerivedStateFromError(error) {
		return { error };
	}

	componentDidCatch(error /* , info */) {
		console.error(error); // eslint-disable-line no-console
	}

	render() {
		return !this.state.error ? this.props.children : false;
	}
}

ErrorBoundary.propTypes = {
	children: PropTypes.node.isRequired,
};

export default ErrorBoundary;
