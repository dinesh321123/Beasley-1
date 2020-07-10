import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

class StnBarker extends PureComponent {
	// eslint-disable-next-line no-useless-constructor
	constructor(props) {
		super(props);
	}

	componentDidMount() {
		console.log('mounted');
		const { placeholder, fk } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const stndiv = document.createElement('div');
		stndiv.className = `s2nPlayer-${fk}`;
		stndiv.setAttribute('data-type', 'barker');

		const stn_barker_script = document.createElement('script');
		stn_barker_script.setAttribute('type', 'text/javascript');
		stn_barker_script.setAttribute(
			'src',
			`//embed.sendtonews.com/player2/embedcode.php?fk=${fk}&cid=10462&SIZE=400`,
		);
		stn_barker_script.setAttribute('data-type', 's2nScript');

		container.appendChild(stndiv);
		container.appendChild(stn_barker_script);
	}

	render() {
		return false;
	}
}

StnBarker.propTypes = {
	placeholder: PropTypes.string.isRequired,
	fk: PropTypes.string.isRequired,
};

export default connect()(StnBarker);
