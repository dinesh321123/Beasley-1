import React, { PureComponent, Fragment } from 'react';
import firebase from 'firebase';

import Header from './elements/Header';
import Alert from './elements/Alert';

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
					<div className="modal-form-actions">
						<button className="button -sign-in" type="submit">
							Restore
						</button>
					</div>
				</form>
			</Fragment>
		);
	}

}

export default RestorePassword;
