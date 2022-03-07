import { PureComponent } from 'react';
import PropTypes from 'prop-types';

//	'<div class="mapbox" data-accesstoken="%s" data-style="%s" data-long="%s" data-lat="%s" data-zoom="%s"></div>',
class MapBox extends PureComponent {
	componentDidMount() {
		const { placeholder, accesstoken, style, long, lat, zoom } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const mapboxscript = document.createElement('script');
		mapboxscript.setAttribute(
			'src',
			'https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js',
		);

		mapboxscript.onload = () => {
			const mapdiv = document.createElement('div');
			mapdiv.setAttribute('id', 'mapboxdiv');
			mapdiv.setAttribute('style', 'width: 100%; height: 400px');

			container.appendChild(mapdiv);

			// eslint-disable-next-line no-undef
			mapboxgl.accessToken = accesstoken;

			// eslint-disable-next-line no-unused-vars,no-undef
			const map = new mapboxgl.Map({
				container: 'mapboxdiv',
				style,
				center: [parseFloat(long), parseFloat(lat)],
				zoom: parseInt(zoom, 10),
			});
		};

		container.appendChild(mapboxscript);

		const mapboxstyle = document.createElement('link');
		mapboxstyle.setAttribute('rel', 'stylesheet');
		mapboxstyle.setAttribute(
			'href',
			'https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css',
		);

		container.appendChild(mapboxstyle);
	}

	render() {
		return false;
	}
}

MapBox.propTypes = {
	placeholder: PropTypes.string.isRequired,
	accesstoken: PropTypes.string,
	style: PropTypes.string,
	long: PropTypes.string,
	lat: PropTypes.string,
	zoom: PropTypes.string,
};

MapBox.defaultProps = {
	accesstoken: '',
	style: '',
	long: '0',
	lat: '0',
	zoom: '0',
};

export default MapBox;
