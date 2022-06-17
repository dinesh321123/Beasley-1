import { PureComponent } from 'react';
import PropTypes from 'prop-types';

//	'<div class="mapbox" data-accesstoken="%s" data-style="%s" data-long="%s" data-lat="%s" data-zoom="%s"></div>',
class Dimers extends PureComponent {
	componentDidMount() {
		const { placeholder } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const dimersscript = document.createElement('script');
		dimersscript.setAttribute(
			'src',
			'https://content-blocks-node.azurewebsites.net/block/pre-match-predictions',
		);

		const dimersdiv = document.createElement('div');
		dimersdiv.id = 'si-external-pre-match-predictions';
		container.appendChild(dimersdiv);

		dimersscript.onload = () => {
			// eslint-disable-next-line no-undef
			loadSiPreMatchPredictions({
				matchID: 'MLB|NBA|NHL|NFL|CFB|CBB_AUTO',

				showGamblingInformation: true,

				publicationTheme: 'beasley',

				oddsDisplayFormat: 'american',

				teamDisplayNameDefault: 'nickname',

				teamLogoDefault: 'official',

				terminologyLocale: 'US',

				liveSportsAvailable: ['MLB', 'NBA', 'NHL', 'NFL', 'CFB', 'CBB'],

				showDimersAttribution: true,

				allowShortlisting: false,

				matchFirstTeam: 'home',

				highlightedTeams: {
					NFL: ['NE'],

					MLB: ['BOS'],

					NHL: ['BOS'],

					NBA: ['BOS'],
				},

				bookmakerTheme: 'fanduel',
			});
		};

		container.appendChild(dimersscript);
	}

	render() {
		return false;
	}
}

Dimers.propTypes = {
	placeholder: PropTypes.string.isRequired,
};

export default Dimers;
