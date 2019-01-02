import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { hideModal, SIGNIN_MODAL, SIGNUP_MODAL, RESTORE_MODAL, COMPLETE_SIGNUP_MODAL } from '../redux/actions/modal';

import SignInModal from '../components/modals/SignIn';
import SignUpModal from '../components/modals/SignUp';
import RestoreModal from '../components/modals/RestorePassword';
import CompleteSignup from '../components/modals/CompleteSignup';

class ModalDispatcher extends Component {

	constructor() {
		super();

		const self = this;
		self.modalRef = React.createRef();

		self.handleEscapeKeyDown = self.handleEscapeKeyDown.bind( self );
		self.handleClickOutside = self.handleClickOutside.bind( self );
	}

	componentDidMount() {
		document.addEventListener( 'mousedown', this.handleClickOutside, false );
		document.addEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	componentWillUnmount() {
		document.removeEventListener( 'mousedown', this.handleClickOutside, false );
		document.removeEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	handleClickOutside( e ) {
		const self = this;
		const { modal } = self.props;
		const { current: ref } = self.modalRef;

		if ( 'CLOSED' !== modal && ( !ref || !ref.contains( e.target ) ) ) {
			self.props.close();
		}
	}

	handleEscapeKeyDown( e ) {
		if ( 27 === e.keyCode ) {
			this.props.close();
		}
	}

	render() {
		const self = this;
		const { modal, payload, close } = self.props;
		let component = false;

		switch( modal ) {
			case SIGNIN_MODAL:
				component = <SignInModal close={close} {...payload} />;
				break;
			case SIGNUP_MODAL:
				component = <SignUpModal close={close} {...payload} />;
				break;
			case RESTORE_MODAL:
				component = <RestoreModal close={close} {...payload} />;
				break;
			case COMPLETE_SIGNUP_MODAL:
				component = <CompleteSignup close={close} {...payload } />;
				break;
			default:
				return false;
		}

		return(
			<div className={`modal ${( modal || '' ).toLowerCase()}`}>
				<div ref={self.modalRef} className="modal-content">
					<button type="button" className="button modal-close" aria-label="Close Modal" onClick={close}>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 212.982 212.982" aria-labelledby="close-modal-title close-modal-desc" width="13" height="13">
							<title id="close-modal-title">Close Modal</title>
							<desc id="close-modal-desc">Checkmark indicating modal close</desc>
							<path d="M131.804 106.491l75.936-75.936c6.99-6.99 6.99-18.323 0-25.312-6.99-6.99-18.322-6.99-25.312 0L106.491 81.18 30.554 5.242c-6.99-6.99-18.322-6.99-25.312 0-6.989 6.99-6.989 18.323 0 25.312l75.937 75.936-75.937 75.937c-6.989 6.99-6.989 18.323 0 25.312 6.99 6.99 18.322 6.99 25.312 0l75.937-75.937 75.937 75.937c6.989 6.99 18.322 6.99 25.312 0 6.99-6.99 6.99-18.322 0-25.312l-75.936-75.936z" fillRule="evenodd" clipRule="evenodd"/>
						</svg>
					</button>
					{component}
				</div>
			</div>
		);
	}
}

ModalDispatcher.propTypes = {
	modal: PropTypes.string,
	payload: PropTypes.shape( {} ),
	close: PropTypes.func.isRequired,
};

ModalDispatcher.defaultProps = {
	modal: '',
	payload: {},
};

function mapStateToProps( { modal } ) {
	return { ...modal };
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( { close: hideModal }, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( ModalDispatcher );
