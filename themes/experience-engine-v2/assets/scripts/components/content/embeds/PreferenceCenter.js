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

		getPreferenceLink(preferenceId).then(r => {
			console.log(r);
			// window.open(r.url, '_blank');
		});
		return false;
	};

	render() {
		return (
			<div>
				{this.state.isLoggedIn ? (
					<div className="user-preference-info">
						{/* eslint-disable-next-line jsx-a11y/anchor-is-valid */}
						<a href="#" rel="noreferrer" onClick={this.getPreferenceLink}>
							Preference Center
						</a>
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
