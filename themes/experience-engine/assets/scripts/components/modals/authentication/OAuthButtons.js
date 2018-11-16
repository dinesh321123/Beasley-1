import React, { PureComponent } from 'react';
import firebase from 'firebase/app';

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
			<div>
				<button type="button" onClick={self.onTwitterLogin}>Twitter</button>
				<button type="button" onClick={self.onFacebookLogin}>Facebook</button>
				<button type="button" onClick={self.onGoogleLogin}>Google</button>
			</div>
		);
	}

}

export default OAuthButtons;
