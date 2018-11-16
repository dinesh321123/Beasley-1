import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreet extends PureComponent {

	componentDidMount() {
		const { placeholder, script, embed, opguid, routing } = this.props;

		const container = document.getElementById( placeholder );
		if ( !container ) {
			return;
		}

		const element = document.createElement( 'script' );

		element.setAttribute( 'async', true );
		element.setAttribute( 'src', script );
		element.setAttribute( 'data-ss-embed', embed );
		element.setAttribute( 'data-opguid', opguid );
		element.setAttribute( 'data-routing', routing );

		container.appendChild( element );
	}

	render() {
		return false;
	}

}

SecondStreet.propTypes = {
	placeholder: PropTypes.string.isRequired,
	script: PropTypes.string,
	embed: PropTypes.string,
	opguid: PropTypes.string,
	routing: PropTypes.string,
};

SecondStreet.defaultProps = {
	script: '',
	embed: '',
	opguid: '',
	routing: '',
};

export default SecondStreet;
