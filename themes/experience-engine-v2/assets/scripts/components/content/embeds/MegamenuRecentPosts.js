import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class MegamenuRecentPosts extends PureComponent {
	componentDidMount = async () => {
		const container = document.getElementById(this.props.placeholder);
		if (!container) {
			return;
		}
		try {
			const { bbgiconfig } = window;
			const {
				postsperpage,
				categories,
				showthumb,
				showthumbsize,
				menuareaid,
			} = this.props;

			// http://985thesportshub.beasley.test/wp-json/megamenu_recent_posts/v1/get_posts?per_page=1&cat=8&show_thumb=1&thumb_size=1
			// const endpointURL = `${bbgiconfig.eeapi}page=${postsperpage}/recommendations?categories=${categories || ''}`;

			container.innerHTML = this.renderLoder('Loading...');
			// const endpointURL = `http://985thesportshub.beasley.test/wp-json/megamenu_recent_posts/v1/get_posts?per_page=${postsperpage}&cat=${categories}&show_thumb=${showthumb}&thumb_size=${showthumbsize}`;
			const endpointURL = `${bbgiconfig['wpapi-mmrp']}get_posts?per_page=${postsperpage}&cat=${categories}&show_thumb=${showthumb}&thumb_size=${showthumbsize}`;
			console.log('endpointURL : ', menuareaid, ' : ', endpointURL);
			const result = await fetch(endpointURL).then(r => r.json());
			console.log(result);
			if (result.recent_posts && result.recent_posts.length) {
				let html = `<ul>`;

				result.recent_posts.forEach(post => {
					html += `
					  <li>
						<a href="${post.permalink}">`;
					if (post.thumbnail) {
						html += `${post.thumbnail}`;
						// html += `<img width="1024" height="683" src="${post.thumbnail}" class="attachment-large size-large wp-post-image" alt="s_pW5jcElAM" loading="lazy"></img>`;
					}
					html += `<span class="rpwwt-post-title">${post.title}</span>
						</a>
					</li>
					`;
				});
				html += `</ul>`;
				console.log(html);
				container.innerHTML = html;
			} else {
				console.error(result);
				container.innerHTML = ``;
			}
		} catch (e) {
			console.error('failed to load api');
			container.innerHTML = ``;
		}
	};

	renderLoder = displayText => {
		return `<div className="loading-ajax-content"> <span className="loading-ajax-content__spinner" /> ${displayText}</div>`;
	};

	render() {
		return false;
	}
}

MegamenuRecentPosts.propTypes = {
	placeholder: PropTypes.string.isRequired,
	postsperpage: PropTypes.number,
	categories: PropTypes.string,
	showthumb: PropTypes.string,
	showthumbsize: PropTypes.string,
	menuareaid: PropTypes.string,
};

MegamenuRecentPosts.defaultProps = {
	postsperpage: '4',
	categories: '',
	showthumb: '',
	showthumbsize: '/',
	menuareaid: '',
};

export default MegamenuRecentPosts;
