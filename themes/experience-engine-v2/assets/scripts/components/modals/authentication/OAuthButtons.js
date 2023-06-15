import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { firebase, firebaseAuth } from '../../../library/firebase';

class OAuthButtons extends PureComponent {
	constructor(props) {
		super(props);

		this.onFacebookLogin = this.handleFacebookLogin.bind(this);
		this.onGoogleLogin = this.handleGoogleLogin.bind(this);
	}

	login(provider) {
		firebaseAuth.signInWithRedirect(provider).catch(error => {
			console.error(error.message); // eslint-disable-line no-console
		});
	}

	handleFacebookLogin() {
		this.login(new firebase.auth.FacebookAuthProvider());
	}

	handleGoogleLogin() {
		this.login(new firebase.auth.GoogleAuthProvider());
	}

	render() {
		return (
			<div className={`oauth${this.props.horizontal ? ' -horizontal' : ''}`}>
				<button
					className="button auth-button -facebook"
					type="button"
					aria-label="Authenticate with Facebook"
					onClick={this.onFacebookLogin}
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="20"
						height="20"
						aria-labelledby="facebook-icon-title facebook-icon-desc"
						viewBox="0 0 90 90"
					>
						<title id="facebook-icon-title">Twitter Brand Logo</title>
						<desc id="facebook-icon-desc">Twitter Bird</desc>
						<path
							d="M90 15.001C90 7.119 82.884 0 75 0H15C7.116 0 0 7.119 0 15.001v59.998C0 82.881 7.116 90 15.001 90H45V56H34V41h11v-5.844C45 25.077 52.568 16 61.875 16H74v15H61.875C60.548 31 59 32.611 59 35.024V41h15v15H59v34h16c7.884 0 15-7.119 15-15.001V15.001z"
							fill="#FFF"
						/>
					</svg>
					Facebook
				</button>
				<button
					className="button auth-button -google"
					type="button"
					aria-label="Authenticate with Google"
					onClick={this.onGoogleLogin}
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 491.858 491.858"
						aria-labelledby="google-icon-title google-icon-desc"
						width="20"
						height="20"
					>
						<title id="google-icon-title">Google Logo</title>
						<desc id="google-icon-desc">Google Brand Logo - G</desc>
						<path
							d="M377.472 224.957H201.319v58.718H308.79c-16.032 51.048-63.714 88.077-120.055 88.077-69.492 0-125.823-56.335-125.823-125.824 0-69.492 56.333-125.823 125.823-125.823 34.994 0 66.645 14.289 89.452 37.346l42.622-46.328c-34.04-33.355-80.65-53.929-132.074-53.929C84.5 57.193 0 141.693 0 245.928s84.5 188.737 188.736 188.737c91.307 0 171.248-64.844 188.737-150.989v-58.718l-.001-.001z"
							fill="#FFF"
						/>
					</svg>
					Google
				</button>
			</div>
		);
	}
}

OAuthButtons.propTypes = {
	horizontal: PropTypes.bool,
};

OAuthButtons.defaultProps = {
	horizontal: false,
};

export default OAuthButtons;
