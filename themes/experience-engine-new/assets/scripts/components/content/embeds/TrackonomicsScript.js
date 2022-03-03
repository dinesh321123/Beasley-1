import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class TrackonomicsScript extends PureComponent {
	componentDidMount() {
		const { postid, posttype, trackonomicsscript } = this.props;
		const trackonomicsScriptID = document.getElementById(
			'funnel-relay-installer',
		);
		if (trackonomicsscript === '1') {
			console.log('Post ID: ', postid);
			console.log('Post type: ', posttype);
			console.log('Trackonomics Script: ', trackonomicsscript);
			console.log('Trackonomics Script status : Show');
			console.log(trackonomicsScriptID);
			if (!trackonomicsScriptID) {
				console.log('Trackonomics Script Not exist');
				const script = document.createElement('script');
				script.id = 'funnel-relay-installer';
				script.setAttribute('data-property-id', 'PROPERTY_ID');
				script.setAttribute('data-customer-id', 'bbgi_39ea5_bbgi');
				script.src = `https://cdn-magiclinks.trackonomics.net/client/static/v2/bbgi_39ea5_bbgi.js`;
				script.async = true;
				document.body.appendChild(script);
			}
		} else {
			console.log('Trackonomics Script status : Remove');
			if (trackonomicsScriptID) {
				trackonomicsScriptID.remove();
			}
		}
	}

	componentWillUnmount() {}

	render() {
		return false;
	}
}

TrackonomicsScript.propTypes = {
	postid: PropTypes.string,
	posttype: PropTypes.string,
	trackonomicsscript: PropTypes.string,
};

TrackonomicsScript.defaultProps = {
	postid: '',
	posttype: '',
	trackonomicsscript: '',
};

export default TrackonomicsScript;
