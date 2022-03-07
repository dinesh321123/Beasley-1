import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class DraftkingEmbed extends PureComponent {
	componentDidMount() {
		const { postid, ishidden } = this.props;
		console.log('Post ID: ', postid);
		console.log('Show DraftKing iFrame: ', ishidden);

		if (ishidden === '0') {
			document.documentElement.style.setProperty(
				'--configurable-iframe-height',
				'0px',
			);
		} else {
			document.documentElement.style.setProperty(
				'--configurable-iframe-height',
				document.documentElement.style.getPropertyValue(
					'--default-configurable-iframe-height',
				),
			);
		}
	}

	componentWillUnmount() {
		document.documentElement.style.setProperty(
			'--configurable-iframe-height',
			document.documentElement.style.getPropertyValue(
				'--default-configurable-iframe-height',
			),
		);
	}

	render() {
		return false;
	}
}

DraftkingEmbed.propTypes = {
	postid: PropTypes.string,
	ishidden: PropTypes.string,
};

DraftkingEmbed.defaultProps = {
	postid: '',
	ishidden: '',
};

export default DraftkingEmbed;
