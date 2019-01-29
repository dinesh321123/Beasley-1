import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import {
	hideModal,
	SIGNIN_MODAL,
	SIGNUP_MODAL,
	RESTORE_MODAL,
	COMPLETE_SIGNUP_MODAL,
	DISCOVER_MODAL,
	EDIT_FEED_MODAL,
} from '../redux/actions/modal';

import CloseButton from '../components/modals/elements/Close';
import SignInModal from '../components/modals/SignIn';
import SignUpModal from '../components/modals/SignUp';
import RestoreModal from '../components/modals/RestorePassword';
import DiscoverModal from '../components/modals/Discover';
import CompleteSignup from '../components/modals/CompleteSignup';
import EditFeedModal from '../components/modals/EditFeed';

class ModalDispatcher extends Component {

	constructor( props ) {
		super( props );

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

		if ( 'CLOSED' !== modal && DISCOVER_MODAL !== modal && ( !ref || !ref.contains( e.target ) ) ) {
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
			case DISCOVER_MODAL:
				component = (
					<div className="discover-modal" ref={self.modalRef}>
						<DiscoverModal close={close} {...payload} />
					</div>
				);

				return ReactDOM.createPortal( component, document.getElementById( 'inner-content' ) );
			case COMPLETE_SIGNUP_MODAL:
				component = <CompleteSignup close={close} {...payload } />;
				break;
			case EDIT_FEED_MODAL:
				component = <EditFeedModal close={close} {...payload} />;
				break;
			default:
				return false;
		}

		return (
			<div className={`modal ${( modal || '' ).toLowerCase()}`}>
				<div ref={self.modalRef} className="modal-content">
					<CloseButton close={close} />
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
	modal: 'CLOSED',
	payload: {},
};

function mapStateToProps( { modal } ) {
	return { ...modal };
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( { close: hideModal }, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( ModalDispatcher );
