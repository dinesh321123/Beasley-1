import React, { Component } from 'react';
import { deleteUser } from '../../../library/experience-engine';
import { firebaseAuth } from '../../../library';

class CancelAccount extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isLoggedIn: false,
			showPrompt: false,
		};
		this.handleYes = this.handleYes.bind(this);
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
		deleteUser().then(r => this.setState({ showPrompt: false }));
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
