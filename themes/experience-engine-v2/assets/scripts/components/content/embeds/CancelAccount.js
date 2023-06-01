import React, { Component } from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { deleteUser } from '../../../library/experience-engine';
import { firebaseAuth } from '../../../library';
import * as authActions from '../../../redux/actions/auth';

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
		const { setUser } = this.props;
		if (config.projectId) {
			firebaseAuth.onAuthStateChanged(
				function(user) {
					if (user) {
						setUser(user);
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
		const { user } = this.props;
		const container = document.querySelectorAll('.info_account')[0];
		container.innerHTML = `<p><strong>Email ID:</strong> <span className="user-email-id">${user.email}</span></p>`;
		return (
			<div>
				{this.state.isLoggedIn ? (
					<div className="user-account-info">
						<button
							type="button"
							className="cancellation"
							onClick={this.handleCancelAccount}
						>
							Cancel Account
						</button>
					</div>
				) : null}
				{this.state.showPrompt ? (
					<div className="prompt-container">
						<h3>Alert Message</h3>
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

CancelAccount.propTypes = {
	setUser: PropTypes.func.isRequired,
	user: PropTypes.oneOfType([PropTypes.object, PropTypes.bool]).isRequired,
};

export default connect(
	({ auth }) => ({
		user: auth.user || false,
	}),
	{
		setUser: authActions.setUser,
	},
)(CancelAccount);
