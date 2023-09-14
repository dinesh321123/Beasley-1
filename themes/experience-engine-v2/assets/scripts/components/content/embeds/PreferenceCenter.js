import React, { Component } from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { getPreferenceLink } from '../../../library/experience-engine';
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
			site_braze_preference_id: preferenceId,
		} = window.bbgiconfig;
		const { setUser } = this.props;

		if (!preferenceId) {
			return;
		}
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

	getPreferenceLink = () => {
		const { site_braze_preference_id: preferenceId } = window.bbgiconfig;

		getPreferenceLink(preferenceId)
			.then(response => response.json())
			.then(result => {
				if (result.Success) {
					window.open(result.URL, '_blank');
				} else if (result?.Error) {
					this.setState({ api_error: result.Error });
				} else {
					this.setState({ api_error: 'Somethings wents wronge!' });
				}
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
							onClick={this.getPreferenceLink}
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
