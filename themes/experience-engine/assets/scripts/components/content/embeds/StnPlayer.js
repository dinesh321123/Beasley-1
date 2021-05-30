import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

class StnPlayer extends PureComponent {
	// eslint-disable-next-line no-useless-constructor
	constructor(props) {
		super(props);
	}

	componentDidMount() {
		console.log('mounted');
		const { placeholder, fk, cid } = this.props;

		if (!window.stnvideos) {
			window.stnvideos = {};
		}

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		console.log('component created');

		window.stnvideos.default = {
			render: () => {
				const stndiv = document.createElement('div');
				stndiv.className = `s2nPlayer k-${fk}`;
				stndiv.setAttribute('data-type', 'float');

				const stn_barker_script = document.createElement('script');
				stn_barker_script.setAttribute('type', 'text/javascript');
				stn_barker_script.setAttribute(
					'src',
					`//embed.sendtonews.com/player3/embedcode.js?fk=${fk}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
				);
				stn_barker_script.setAttribute('data-type', 's2nScript');

				container.appendChild(stndiv);
				container.appendChild(stn_barker_script);
			},
			type: 'default',
		};
	}

	render() {
		return false;
	}
}

StnPlayer.propTypes = {
	placeholder: PropTypes.string.isRequired,
	fk: PropTypes.string.isRequired,
	cid: PropTypes.string.isRequired,
};

export default connect()(StnPlayer);
