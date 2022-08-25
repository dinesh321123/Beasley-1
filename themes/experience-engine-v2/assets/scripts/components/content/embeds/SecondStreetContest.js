import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class SecondStreetContest extends PureComponent {
	componentDidMount() {
		const { placeholder, contest_url, routing, contest_id } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const contest_script = `${contest_url}/shared/embedcode/talker-v1.0.0.js`;
		const script = `${contest_url}/shared/embedcode/embed.js`;

		const contestElement = document.createElement('script');

		contestElement.setAttribute('type', 'text/javascript');
		contestElement.setAttribute('src', contest_script);
		container.appendChild(contestElement);

		const element = document.createElement('script');
		element.setAttribute('async', true);
		element.setAttribute('src', script);
		element.setAttribute('data-ss-embed', 'contest');
		element.setAttribute('data-routing', routing);
		element.setAttribute('data-contest-id', contest_id);
		container.appendChild(element);
	}

	render() {
		return false;
	}
}

SecondStreetContest.propTypes = {
	placeholder: PropTypes.string.isRequired,
	contest_url: PropTypes.string,
	routing: PropTypes.string,
	contest_id: PropTypes.string,
};

SecondStreetContest.defaultProps = {
	contest_url: '',
	routing: '',
	contest_id: '',
};

export default SecondStreetContest;
