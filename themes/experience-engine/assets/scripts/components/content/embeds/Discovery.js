import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import LazyImage from './LazyImage';
import {
	showSignInModal,
	showDiscoverModal,
} from '../../../redux/actions/modal';

class Discovery extends PureComponent {
	constructor(props) {
		super(props);

		this.handleClick = this.handleClick.bind(this);
	}

	handleClick() {
		const { signedIn, showDiscover, showSignin } = this.props;
		const menuItems = document.querySelectorAll('#menu-ee-primary li');
		const discoveryWrapper = document.querySelector('#menu-item-discovery');
		const navWrapper = document.querySelector('.nav-wrap');

		for (let i = 0; i < menuItems.length; i++) {
			menuItems[i].classList.remove('current-menu-item');
		}

		if (!discoveryWrapper.classList.contains('current-menu-item')) {
			discoveryWrapper.classList.add('current-menu-item');
		}

		if (navWrapper) {
			navWrapper.classList.remove('is-active');
		}

		if (signedIn) {
			showDiscover();
		} else {
			showSignin();
		}
	}

	render() {
		const { placeholder } = this.props;
		const { bbgiconfig } = window;
		const { publisher, theme } = bbgiconfig || {};
		const { title } = publisher || {};
		const { logo } = theme || {};

		let logoImage = false;
		if (logo && logo.url) {
			const id = `${placeholder}-image`;
			const { url, width, height } = logo;

			logoImage = (
				<div id={id} className="image">
					<LazyImage
						crop={false}
						placeholder={id}
						src={url}
						width={`${width}`}
						height={`${height}`}
						alt={title || ''}
					/>
				</div>
			);
		}

		return (
			<div className="content-wrap">
				<div className="meta">
					{logoImage}

					<div className="copy">
						<h3>Personalize your feed</h3>
						<p>Subscribe to content tailored just for you</p>
					</div>
				</div>

				<div className="action">
					<svg className="waveform">
						<rect
							style={{ animationDelay: '.2s' }}
							width="7"
							height="42"
							x="198"
							y="41"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.4s' }}
							width="7"
							height="42"
							x="246"
							y="41"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.6s' }}
							width="7"
							height="72"
							x="210"
							y="26"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.8s' }}
							width="7"
							height="72"
							x="234"
							y="26"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1s' }}
							width="7"
							height="72"
							x="258"
							y="26"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.2s' }}
							width="7"
							height="42"
							x="270"
							y="41"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.4s' }}
							width="7"
							height="42"
							x="294"
							y="41"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.6s' }}
							width="7"
							height="19"
							x="306"
							y="52"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.8s' }}
							width="7"
							height="9"
							x="318"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.2s' }}
							width="7"
							height="9"
							x="330"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.4s' }}
							width="7"
							height="5"
							x="342"
							y="59"
							rx="2.5"
						/>
						<rect
							style={{ animationDelay: '.6s' }}
							width="7"
							height="9"
							x="354"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.8s' }}
							width="7"
							height="5"
							x="366"
							y="59"
							rx="2.5"
						/>
						<rect
							style={{ animationDelay: '1s' }}
							width="7"
							height="9"
							x="378"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.2s' }}
							width="7"
							height="5"
							x="138"
							y="59"
							rx="2.5"
						/>
						<rect
							style={{ animationDelay: '1.4s' }}
							width="7"
							height="9"
							x="150"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.6s' }}
							width="7"
							height="5"
							x="162"
							y="59"
							rx="2.5"
						/>
						<rect
							style={{ animationDelay: '1.8s' }}
							width="7"
							height="9"
							x="174"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.2s' }}
							width="7"
							height="19"
							x="186"
							y="52"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.4s' }}
							width="7"
							height="72"
							x="282"
							y="26"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.6s' }}
							width="7"
							height="123"
							x="222"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '.8s' }}
							width="7"
							height="42"
							x="79"
							y="43"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1s' }}
							width="7"
							height="42"
							x="55"
							y="43"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.2s' }}
							width="7"
							height="19"
							x="43"
							y="52"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.4s' }}
							width="7"
							height="9"
							x="31"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.6s' }}
							width="7"
							height="9"
							x="19"
							y="57"
							rx="3.5"
						/>
						<rect
							style={{ animationDelay: '1.8s' }}
							width="7"
							height="5"
							x="7"
							y="59.179104"
							rx="2.5"
						/>
						<rect
							style={{ animationDelay: '.2s' }}
							width="7"
							height="72"
							x="67"
							y="28"
							rx="3.5"
						/>
					</svg>

					<button className="btn" onClick={this.handleClick} type="button">
						Customize Your Feed
					</button>
				</div>
			</div>
		);
	}
}

Discovery.propTypes = {
	signedIn: PropTypes.bool.isRequired,
	placeholder: PropTypes.string.isRequired,
	showDiscover: PropTypes.func.isRequired,
	showSignin: PropTypes.func.isRequired,
};

function mapStateToProps({ auth }) {
	return {
		signedIn: !!auth.user,
	};
}

function mapDispatchToProps(dispatch) {
	const actions = {
		showSignin: showSignInModal,
		showDiscover: showDiscoverModal,
	};

	return bindActionCreators(actions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(Discovery);
