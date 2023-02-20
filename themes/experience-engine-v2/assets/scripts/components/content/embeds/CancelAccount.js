import React, { Component } from 'react';
import { firebaseAuth } from '../../../library';

class CancelAccount extends Component {
	constructor(props) {
		console.log('in');
		super(props);
		this.state = {
			isLoggedIn: false,
			showPrompt: false,
		};
	}

	componentDidMount() {
		const { firebase: config } = window.bbgiconfig;
		if (config.projectId) {
			firebaseAuth.onAuthStateChanged(
				function(user) {
					if (user) {
						this.setState({ isLoggedIn: true });
					} else {
						this.setState({ isLoggedIn: false });
					}
				}.bind(this),
			);
		} else {
			// eslint-disable-next-line no-console
			console.error('Firebase Project ID not found in bbgiconfig.');
		}
	}

	handleCancelAccount = () => {
		this.setState({ showPrompt: true });
	};

	handleYes = () => {
		// Get the user's authorization string
		firebaseAuth.currentUser.getIdToken().then(function(idToken) {
			// idToken is the user's authorization string
			const authorizationString = idToken;
			console.log(authorizationString);
			const url = window.bbgiconfig.eeapi;
			// Send delete request to the experience engine
			fetch(`${url}user?authorization=${authorizationString}`, {
				method: 'delete',
				headers: {
					Accept: 'application/json',
					'Content-Type': 'application/json',
				},
				body: null,
			}).then(response => {
				// Log user out
				firebaseAuth.signOut();
				this.setState({ showPrompt: false });
			});
		});
	};

	handleNo = () => {
		this.setState({ showPrompt: false });
	};

	render() {
		return (
			<div>
				{this.state.isLoggedIn ? (
					<button
						type="button"
						className="cancellation"
						onClick={this.handleCancelAccount}
					>
						Cancel Account
					</button>
				) : null}
				{this.state.showPrompt ? (
					<div className="prompt-container">
						<div className="confirmation-text">
							Are you sure you want to cancel your account?
						</div>
						<div className="button-container">
							<button type="button" onClick={this.handleYes}>
								Yes
							</button>
							<button type="button" onClick={this.handleNo}>
								No
							</button>
						</div>
					</div>
				) : null}
			</div>
		);
	}
}

export default CancelAccount;
