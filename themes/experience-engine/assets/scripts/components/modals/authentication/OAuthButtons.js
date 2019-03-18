import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import firebase from 'firebase';

class OAuthButtons extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.onTwitterLogin = self.handleTwitterLogin.bind( self );
		self.onFacebookLogin = self.handleFacebookLogin.bind( self );
		self.onGoogleLogin = self.handleGoogleLogin.bind( self );
	}

	login( provider ) {
		firebase.auth().signInWithRedirect( provider ).catch( error => {
			console.error( error.message ); // eslint-disable-line no-console
		} );
	}

	handleTwitterLogin() {
		this.login( new firebase.auth.TwitterAuthProvider() );
	}

	handleFacebookLogin() {
		this.login( new firebase.auth.FacebookAuthProvider() );
	}

	handleGoogleLogin() {
		this.login( new firebase.auth.GoogleAuthProvider() );
	}

	render() {
		const self = this;

		return (
			<div className={`oauth${this.props.horizontal ? ' -horizontal' : ''}`}>
				<button className="button auth-button -facebook" type="button" aria-label="Authenticate with Facebook" onClick={self.onFacebookLogin}>
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" aria-labelledby="facebook-icon-title facebook-icon-desc" viewBox="0 0 90 90">
						<title id="facebook-icon-title">Twitter Brand Logo</title>
						<desc id="facebook-icon-desc">Twitter Bird</desc>
						<path d="M90 15.001C90 7.119 82.884 0 75 0H15C7.116 0 0 7.119 0 15.001v59.998C0 82.881 7.116 90 15.001 90H45V56H34V41h11v-5.844C45 25.077 52.568 16 61.875 16H74v15H61.875C60.548 31 59 32.611 59 35.024V41h15v15H59v34h16c7.884 0 15-7.119 15-15.001V15.001z" fill="#FFF"/>
					</svg>
					Facebook
				</button>
				<button className="button auth-button -twitter" type="button" aria-label="Authenticate with Twitter" onClick={self.onTwitterLogin}>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 612 612" width="20" aria-labelledby="twitter-icon-title twitter-icon-desc" height="20">
						<title id="twitter-icon-title">Facebook Brand Logo</title>
						<desc id="twitter-icon-desc">Facebook F</desc>
						<path d="M612 116.258a250.714 250.714 0 0 1-72.088 19.772c25.929-15.527 45.777-40.155 55.184-69.411-24.322 14.379-51.169 24.82-79.775 30.48-22.907-24.437-55.49-39.658-91.63-39.658-69.334 0-125.551 56.217-125.551 125.513 0 9.828 1.109 19.427 3.251 28.606-104.326-5.24-196.835-55.223-258.75-131.174-10.823 18.51-16.98 40.078-16.98 63.101 0 43.559 22.181 81.993 55.835 104.479a125.556 125.556 0 0 1-56.867-15.756v1.568c0 60.806 43.291 111.554 100.693 123.104-10.517 2.83-21.607 4.398-33.08 4.398-8.107 0-15.947-.803-23.634-2.333 15.985 49.907 62.336 86.199 117.253 87.194-42.947 33.654-97.099 53.655-155.916 53.655-10.134 0-20.116-.612-29.944-1.721 55.567 35.681 121.536 56.485 192.438 56.485 230.948 0 357.188-191.291 357.188-357.188l-.421-16.253c24.666-17.593 46.005-39.697 62.794-64.861z" fill="#FFF"/>
					</svg>
					Twitter
				</button>
				<button className="button auth-button -google" type="button" aria-label="Authenticate with Google" onClick={self.onGoogleLogin}>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 491.858 491.858" aria-labelledby="google-icon-title google-icon-desc" width="20" height="20">
						<title id="google-icon-title">Google Logo</title>
						<desc id="google-icon-desc">Google Brand Logo - G</desc>
						<path d="M377.472 224.957H201.319v58.718H308.79c-16.032 51.048-63.714 88.077-120.055 88.077-69.492 0-125.823-56.335-125.823-125.824 0-69.492 56.333-125.823 125.823-125.823 34.994 0 66.645 14.289 89.452 37.346l42.622-46.328c-34.04-33.355-80.65-53.929-132.074-53.929C84.5 57.193 0 141.693 0 245.928s84.5 188.737 188.736 188.737c91.307 0 171.248-64.844 188.737-150.989v-58.718l-.001-.001z" fill="#FFF"/>
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

export default OAuthButtons;
