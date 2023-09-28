import React, { Component } from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { getToken } from '../../../library/experience-engine';
import { firebaseAuth } from '../../../library';
import * as authActions from '../../../redux/actions/auth';

class PreferenceCenter extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isLoggedIn: false,
			api_error: '',
		};
	}

	componentDidMount() {
		const {
			firebase: config,
			// site_braze_preference_id: preferenceId,
		} = window.bbgiconfig;
		const { setUser } = this.props;

		// if (!preferenceId) {
		//	return;
		// }

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

	openPreferenceLink = () => {
		getToken().then(token => {
			const emailParam = encodeURIComponent(firebaseAuth?.currentUser?.email);
			const preferenceUrl = `https://preferencecenter${
				window.isDevEnvironment() ? '-staging' : ''
			}.bbgi.com/index.html?callletters=${window.getCallLetters()}&email=${emailParam}&authorization=${token}`;
			window.open(preferenceUrl, '_blank');
		});
	};

	render() {
		return (
			<div>
				{this.state.isLoggedIn ? (
					<div className="user-preference-info">
						Click here to set your email newsletter
						<button
							type="button"
							className="preference-link-btn"
							onClick={this.openPreferenceLink}
						>
							preferences
						</button>
						{this.state.api_error ? <span>{this.state.api_error}</span> : null}
					</div>
				) : null}
			</div>
		);
	}
}

PreferenceCenter.propTypes = {
	setUser: PropTypes.func.isRequired,
};

export default connect(
	({ auth }) => ({
		user: auth.user || false,
	}),
	{
		setUser: authActions.setUser,
	},
)(PreferenceCenter);
