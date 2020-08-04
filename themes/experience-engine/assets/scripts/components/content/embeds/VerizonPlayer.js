import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

class VerizonPlayer extends PureComponent {
	// eslint-disable-next-line no-useless-constructor
	constructor(props) {
		super(props);
	}

	componentDidMount() {
		const { placeholder, pid } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const verizondiv = document.createElement('div');

		verizondiv.className = `vdb_player vdb_${pid}5efa24e76051522e27eabd51`;
		verizondiv.id = `${pid}`;
		verizondiv.setAttribute('data-type', 'float');

		const verizon_script = document.createElement('script');
		verizon_script.setAttribute('type', 'text/javascript');
		verizon_script.setAttribute(
			'src',
			`https://delivery.vidible.tv/jsonp/pid=${pid}/5efa24e76051522e27eabd51.js?`,
		);
		verizon_script.setAttribute('data-type', 's2nScript');

		container.appendChild(verizondiv);
		container.appendChild(verizon_script);
	}

	render() {
		return false;
	}
}

VerizonPlayer.propTypes = {
	placeholder: PropTypes.string.isRequired,
	pid: PropTypes.string.isRequired,
};

export default connect()(VerizonPlayer);
