import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';
import Alert from './elements/Alert';

import { saveUser, validateDate } from '../../library/experience-engine';

class CompleteSignup extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			zip: '',
			gender: '',
			bday: '',
			error: '',
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
		const { zip, gender, bday } = self.state;
		const { user, close } = self.props;

		e.preventDefault();

		// @TODO :: This currently breaks on date specific inputs. We could consider just removing the date input type and using a text input.
		if( false === validateDate( bday ) ) {
			self.setState( { error: 'Please ensure date is in MM/DD/YYYY format' } );
			return false;
		} else {
			self.setState( { error: '' } );
		}

		if ( user ) {
			const { email } = user;
			saveUser( email, zip, gender, bday ).then( close );
		}
	}

	render() {
		const self = this;
		const { zip, gender, bday, error } = self.state;

		return (
			<Fragment>
				<Header>
					<h3>Complete Your Profile</h3>
				</Header>

				<Alert message={error} />

				<form className="modal-form -form-sign-up" onSubmit={self.onFormSubmit}>
					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-zip">Zip</label>
						<input className="modal-form-field" type="text" id="user-zip" name="zip" value={zip} onChange={self.onFieldChange} placeholder="90210" />
					</div>
					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-bday">Birthday</label>
						<input className="modal-form-field" type="date" id="user-bday" name="bday" value={bday} onChange={self.onFieldChange} placeholder="Enter your birthday" />
					</div>
					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-gender-male">Gender</label>
						<div className="modal-form-radio">
							<input type="radio" id="user-gender-male" name="gender" value="male" checked={'male' === gender} onChange={self.onFieldChange} />
							<label htmlFor="user-gender-male">Male</label>
						</div>
						<div className="modal-form-radio">
							<input type="radio" id="user-gender-female" name="gender" value="female" checked={'female' === gender} onChange={self.onFieldChange} />
							<label htmlFor="user-gender-female">Female</label>
						</div>
					</div>
					<div className="modal-form-actions">
						<button className="btn -sign-in" type="submit">Save</button>
					</div>
				</form>
			</Fragment>
		);
	}

}

CompleteSignup.propTypes = {
	close: PropTypes.func.isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	user: PropTypes.oneOfType( [PropTypes.object, PropTypes.bool] ).isRequired,
};

function mapStateToProps( { auth } ) {
	return { user: auth.user || false };
}

export default connect( mapStateToProps )( trapHOC()( CompleteSignup ) );
