import { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

class DelayedComponent extends PureComponent {

	constructor( props ) {
		super( props );
		this.state = { waiting: true };
	}

	componentDidMount() {
		const self = this;
		self.timeoutId = setTimeout( () => {
			self.setState( { waiting: false } );
		}, 50 );
	}

	componentWillUnmount() {
		const self = this;
		if ( self.timeoutId ) {
			clearTimeout( self.timeoutId );
		}
	}

	render() {
		const self = this;
		const { children, placeholder } = self.props;
		const { waiting } = self.state;

		if ( waiting ) {
			return false;
		}

		const container = document.getElementById( placeholder );
		if ( !container ) {
			return false;
		}

		return ReactDOM.createPortal( children, container );
	}

}

DelayedComponent.propTypes = {
	placeholder: PropTypes.string.isRequired,
	children: PropTypes.oneOfType( [PropTypes.node, PropTypes.array] ).isRequired,
};

export default DelayedComponent;
