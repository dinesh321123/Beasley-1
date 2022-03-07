import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import trapHOC from '@10up/react-focus-trap-hoc';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { firebaseAuth } from '../../library/firebase';
import Header from './elements/Header';
import Alert from './elements/Alert';

import { showSignInModal } from '../../redux/actions/modal';

class RestorePassword extends PureComponent {
	constructor(props) {
		super(props);

		this.state = {
			email: '',
			message: '',
			success: false,
		};

		this.onFieldChange = this.handleFieldChange.bind(this);
		this.onFormSubmit = this.handleFormSubmit.bind(this);
	}

	componentDidMount() {
		this.props.activateTrap();
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
	}

	handleFieldChange(e) {
		const { target } = e;
		this.setState({ [target.name]: target.value });
	}

	handleFormSubmit(e) {
		const { email } = this.state;

		e.preventDefault();

		firebaseAuth
			.sendPasswordResetEmail(email, { url: window.location.href })
			.then(() => {
				this.setState({
					success: true,
					email: '',
					message:
						'Please, check your inbox. An email has been sent to you with instructions how to reset your password.',
				});
			})
			.catch(error => {
				this.setState({
					message: error.message,
				});
			});
	}

	render() {
		const { email, message, success } = this.state;
		const { signin } = this.props;

		return (
			<>
				<Header>
					<h3>Restore Password</h3>
				</Header>

				<Alert message={message} type={success ? 'info' : 'error'} />

				<form className="modal-form" onSubmit={this.onFormSubmit}>
					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-email">
							Email
						</label>
						<input
							className="modal-form-field"
							type="email"
							id="user-email"
							name="email"
							value={email}
							onChange={this.onFieldChange}
							placeholder="yourname@yourddomain.com"
						/>
					</div>

					<div className="modal-form-actions -signup -restore">
						<button className="btn -sign-in" type="submit">
							Restore
						</button>
						<p>
							<strong>Already a member?</strong>{' '}
							<button
								className="btn -empty -nobor -sign-in"
								type="button"
								onClick={signin}
							>
								Sign In
							</button>
						</p>
					</div>
				</form>
			</>
		);
	}
}

RestorePassword.propTypes = {
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	signin: PropTypes.func.isRequired,
};

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			signin: showSignInModal,
		},
		dispatch,
	);
}
export default connect(null, mapDispatchToProps)(trapHOC()(RestorePassword));
