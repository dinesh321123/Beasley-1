import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import ReactTooltip from 'react-tooltip';

const getShareIndex = () => {
	let { socialShareIndex } = window;
	if (!socialShareIndex) {
		window.socialShareIndex = 1;
		socialShareIndex = window.socialShareIndex;
	} else {
		window.socialShareIndex++;
		socialShareIndex = window.socialShareIndex;
	}

	return socialShareIndex;
};

class Share extends PureComponent {
	constructor(props) {
		super(props);

		this.onFacebookClick = this.handleFacebookClick.bind(this);
		this.onTwitterClick = this.handleTwitterClick.bind(this);
		this.onCopyUrlClick = this.handleCopyUrlClick.bind(this);
		this.copyref = React.createRef();
	}

	componentWillUnmount() {
		window.socialShareIndex = 0;
	}

	getUrl() {
		const { url } = this.props;
		return encodeURIComponent(url || window.location.href);
	}

	getTitle() {
		const { title } = this.props;
		return encodeURIComponent(title || document.title);
	}

	handleFacebookClick() {
		const url = `https://www.facebook.com/sharer/sharer.php?u=${this.getUrl()}&t=${this.getTitle()}`;

		window.open(
			url,
			'',
			'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600',
		);
	}

	handleTwitterClick() {
		const url = `https://twitter.com/share?url=${this.getUrl()}&text=${this.getTitle()}`;

		window.open(
			url,
			'',
			'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600',
		);
	}

	handleCopyUrlClick() {
		const { url, title } = this.props;
		const el = document.createElement('input');
		el.value = `${title} ${url}`;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		setTimeout(() => {
			ReactTooltip.hide(this.copyref.current);
		}, 3000);
	}

	render() {
		const shareIndex = getShareIndex();
		return (
			<div className="share">
				<button
					className="facebook"
					onClick={this.onFacebookClick}
					aria-label="Share this on Facebook"
					type="button"
				>
					<svg xmlns="http://www.w3.org/2000/svg" width="8" height="17">
						<path d="M4.78 16.224H1.911v-7.65H0V5.938l1.912-.001-.003-1.553c0-2.151.583-3.46 3.117-3.46h2.11v2.637H5.816c-.987 0-1.034.368-1.034 1.056l-.004 1.32H7.15l-.28 2.636H4.781l-.002 7.65z" />
					</svg>
				</button>
				<button
					className="twitter"
					onClick={this.onTwitterClick}
					aria-label="Share this on Twitter"
					type="button"
				>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="14">
						<path d="M15.13 2.38a6.207 6.207 0 0 1-1.783.489 3.114 3.114 0 0 0 1.365-1.718c-.6.356-1.264.614-1.971.754a3.104 3.104 0 0 0-5.29 2.831 8.813 8.813 0 0 1-6.398-3.244 3.103 3.103 0 0 0 .96 4.144 3.091 3.091 0 0 1-1.405-.388v.04a3.106 3.106 0 0 0 2.49 3.043 3.11 3.11 0 0 1-1.402.053 3.107 3.107 0 0 0 2.9 2.156A6.227 6.227 0 0 1 0 11.825a8.785 8.785 0 0 0 4.758 1.395c5.71 0 8.832-4.73 8.832-8.832a8.92 8.92 0 0 0-.009-.401A6.305 6.305 0 0 0 15.13 2.38z" />
					</svg>
				</button>
				<button
					ref={this.copyref}
					className="copyurl"
					aria-label="Copy Url"
					type="button"
					data-event="click"
					data-tip="Link Copied!"
					data-for={`copyurltooltip${shareIndex}`}
				>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
						<path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z" />
						<path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z" />
					</svg>
				</button>
				<ReactTooltip
					place="right"
					effect="solid"
					afterShow={this.onCopyUrlClick}
					id={`copyurltooltip${shareIndex}`}
					globalEventOff="click"
				/>
			</div>
		);
	}
}

Share.propTypes = {
	url: PropTypes.string,
	title: PropTypes.string,
};

Share.defaultProps = {
	url: '',
	title: '',
};

export default Share;
