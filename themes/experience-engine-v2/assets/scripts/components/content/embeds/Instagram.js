import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class Instagram extends PureComponent {
	componentDidMount() {
		const { content, placeholder } = this.props;

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		const contentwithoutscript = content.replace(/<script.*<\/script>/, '');

		const div = document.createElement('span');
		div.innerHTML = contentwithoutscript;

		container.appendChild(div);

		const element = document.createElement('script');

		element.setAttribute('src', '//platform.instagram.com/en_US/embeds.js');

		container.appendChild(element);

		window.instgrm.Embeds.process();
	}

	render() {
		return false;
	}
}

Instagram.propTypes = {
	placeholder: PropTypes.string.isRequired,
	content: PropTypes.string,
};

Instagram.defaultProps = {
	content: '',
};

export default Instagram;
