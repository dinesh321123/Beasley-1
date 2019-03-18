import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import firebase from 'firebase';
import trapHOC from '@10up/react-focus-trap-hoc';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import Header from './elements/Header';
import Alert from './elements/Alert';

import { showSignInModal } from '../../redux/actions/modal';

class RestorePassword extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			email: '',
			message: '',
			success: false,
		};

		self.onFieldChange = self.handleFieldChange.bind( self );
		self.onFormSubmit = self.handleFormSubmit.bind( self );
	}

	componentDidMount() {
		this.props.activateTrap();
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
	}

	handleFieldChange( e ) {
		const { target } = e;
		this.setState( { [target.name]: target.value } );
	}

	handleFormSubmit( e ) {
		const self = this;
		const { email } = self.state;
		const auth = firebase.auth();

		e.preventDefault();

		auth.sendPasswordResetEmail( email, { url: window.location.href } )
			.then( () => {
				self.setState( {
					success: true,
					email: '',
					message: 'Please, check your inbox. An email has been sent to you with instructions how to reset your password.',
				} );
			} )
			.catch( ( error ) => {
				self.setState( {
					message: error.message,
				} );
			} );
	}

	render() {
		const self = this;
		const {
			email,
			message,
			success
		} = self.state;
		const { signin } = self.props;

		return (
			<Fragment>
				<Header>
					<h3>Restore Password</h3>
				</Header>

				<Alert message={message} type={success ? 'info' : 'error'} />

				<form className="modal-form" onSubmit={self.onFormSubmit}>
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
							onChange={self.onFieldChange}
							placeholder="yourname@yourddomain.com"
						/>
					</div>

					<div className="modal-form-actions -signup -restore">
						<button className="btn -sign-in" type="submit">Restore</button>
						<p><strong>Already a member?</strong> <button className="btn -empty -nobor -sign-in" type="button" onClick={signin}>Sign In</button></p>
					</div>
				</form>
			</Fragment>
		);
	}

}

RestorePassword.propTypes = {
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	signin: PropTypes.func.isRequired,
};

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		signin: showSignInModal,
	}, dispatch );
}
export default connect( null, mapDispatchToProps )( trapHOC()( RestorePassword ) );
