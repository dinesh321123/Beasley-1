import React, { PureComponent, Fragment } from 'react';
import firebase from 'firebase';
import md5 from 'md5';

import Header from './elements/Header';
import Alert from './elements/Alert';
import OAuthButtons from './authentication/OAuthButtons';

class SignUp extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			email: '',
			password: '',
			firstname: '',
			lastname: '',
			zip: '',
			gender: '',
			bday: '',
			error: '',
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
		const { email, password, firstname, lastname } = self.state;
		const auth = firebase.auth();

		const emailAddress = email.trim().toLowerCase();
		const userData = {
			displayName: `${firstname} ${lastname}`,
			photoURL: `//www.gravatar.com/avatar/${md5( emailAddress )}.jpg?s=100`,
		};

		e.preventDefault();

		auth.createUserWithEmailAndPassword( emailAddress, password )
			.then( response => response.user.updateProfile( userData ) )
			.then( () => self.props.close() )
			.catch( error => self.setState( { error: error.message } ) );
	}

	render() {
		const self = this;
		const { email, password, firstname, lastname, zip, gender, bday, error } = self.state;

		return (
			<Fragment>
				<Header>Sign Up</Header>

				<Alert message={error} />

				<form onSubmit={self.onFormSubmit}>
					<div>
						<label htmlFor="user-email">Email</label>
						<input type="email" id="user-email" name="email" value={email} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-password">Password</label>
						<input type="password" id="user-password" name="password" value={password} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-firstname">First Name</label>
						<input type="text" id="user-firstname" name="firstname" value={firstname} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-lastname">Last Name</label>
						<input type="text" id="user-lastname" name="lastname" value={lastname} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-zip">Zip</label>
						<input type="text" id="user-zip" name="zip" value={zip} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-bday">Birthday</label>
						<input type="date" id="user-bday" name="bday" value={bday} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-gender-male">Gender</label>
						<div>
							<input type="radio" id="user-gender-male" name="gender" value="male" checked={'male' === gender} onChange={self.onFieldChange} />
							<label htmlFor="user-gender-male">Male</label>
						</div>
						<div>
							<input type="radio" id="user-gender-female" name="gender" value="female" checked={'female' === gender} onChange={self.onFieldChange} />
							<label htmlFor="user-gender-female">Female</label>
						</div>
					</div>
					<div>
						<button type="submit">Sign Up</button>
					</div>
				</form>

				<OAuthButtons />
			</Fragment>
		);
	}

}

export default SignUp;
