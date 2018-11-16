import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { hideModal, SIGNIN_MODAL, SIGNUP_MODAL, RESTORE_MODAL } from '../redux/actions/modal';

import SignInModal from '../components/modals/SignIn';
import SignUpModal from '../components/modals/SignUp';
import RestoreModal from '../components/modals/RestorePassword';

const ModalDispatcher = ( { modal, payload, close } ) => {
	let component = false;
	switch ( modal ) {
		case SIGNIN_MODAL:
			component = <SignInModal close={close} {...payload} />;
			break;
		case SIGNUP_MODAL:
			component = <SignUpModal close={close} {...payload} />;
			break;
		case RESTORE_MODAL:
			component = <RestoreModal close={close} {...payload} />;
			break;
		default:
			return false;
	}

	return (
		<div className={`modal ${( modal || '' ).toLowerCase()}`}>
			<div className="modal-content">
				<button type="button" className="modal-close" onClick={close}>X</button>
				{component}
			</div>
		</div>
	);
};

ModalDispatcher.propTypes = {
	modal: PropTypes.string,
	payload: PropTypes.shape( {} ),
	close: PropTypes.func.isRequired,
};

ModalDispatcher.defaultProps = {
	modal: '',
	payload: {},
};

const mapStateToProps = ( { modal } ) => ( { ...modal } );

const mapDispatchToProps = ( dispatch ) => bindActionCreators( { close: hideModal }, dispatch );

export default connect( mapStateToProps, mapDispatchToProps )( ModalDispatcher );
