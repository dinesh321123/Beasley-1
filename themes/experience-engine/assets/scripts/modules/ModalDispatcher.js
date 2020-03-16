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

import { setNavigationRevert } from '../redux/actions/navigation';

import ErrorBoundary from '../components/ErrorBoundary';
import CloseButton from '../components/modals/elements/Close';
import SignInModal from '../components/modals/SignIn';
import SignUpModal from '../components/modals/SignUp';
import RestoreModal from '../components/modals/RestorePassword';
import DiscoverModal from '../components/modals/Discover';
import CompleteSignup from '../components/modals/CompleteSignup';
import EditFeedModal from '../components/modals/EditFeed';

class ModalDispatcher extends Component {
	constructor(props) {
		super(props);

		this.modalRef = React.createRef();

		this.handleEscapeKeyDown = this.handleEscapeKeyDown.bind(this);
		this.handleClickOutside = this.handleClickOutside.bind(this);
	}

	componentDidMount() {
		document.addEventListener('mousedown', this.handleClickOutside, false);
		document.addEventListener('keydown', this.handleEscapeKeyDown, false);
	}

	componentWillUnmount() {
		document.removeEventListener('mousedown', this.handleClickOutside, false);
		document.removeEventListener('keydown', this.handleEscapeKeyDown, false);
	}

	handleMenuCurrentItem() {
		const { navigation } = this.props;
		const { previous: previousMenuItem } = navigation;
		const previous = document.getElementById(previousMenuItem);

		// If Discovery was toggled by a non-menu item and a current item doesn't exist, deselect all items
		const menuItems = document.querySelectorAll('#menu-ee-primary li');

		for (let i = 0; i < menuItems.length; i++) {
			menuItems[i].classList.remove('current-menu-item');
		}

		if (previous) {
			previous.classList.add('current-menu-item');
		} else {
			// If Discovery was toggled by a non-menu item and a previous item doesn't appear, select 'Home'
			const homeButton = document.getElementById('menu-item-home');
			homeButton.classList.add('current-menu-item');
		}

		this.props.navigationRevert();
	}

	handleClickOutside(e) {
		const { modal } = this.props;
		const { current: ref } = this.modalRef;

		if (
			modal !== 'CLOSED' &&
			DISCOVER_MODAL !== modal &&
			COMPLETE_SIGNUP_MODAL !== modal &&
			(!ref || !ref.contains(e.target))
		) {
			this.props.close();
			this.handleMenuCurrentItem();
		}
	}

	handleEscapeKeyDown(e) {
		const { modal } = this.props;

		if (e.keyCode === 27 && COMPLETE_SIGNUP_MODAL !== modal) {
			this.props.close();
			this.handleMenuCurrentItem();
		}
	}

	handleClose() {
		this.props.close();
		this.handleMenuCurrentItem();
	}

	render() {
		const { modal, payload } = this.props;

		let component = false;

		/* eslint-disable react/jsx-props-no-spreading */
		switch (modal) {
			case SIGNIN_MODAL:
				component = (
					<SignInModal close={() => this.handleClose()} {...payload} />
				);
				break;
			case SIGNUP_MODAL:
				component = (
					<SignUpModal close={() => this.handleClose()} {...payload} />
				);
				break;
			case RESTORE_MODAL:
				component = (
					<RestoreModal close={() => this.handleClose()} {...payload} />
				);
				break;
			case DISCOVER_MODAL:
				component = (
					<div className="discover-modal" ref={this.modalRef}>
						<DiscoverModal close={() => this.handleClose()} {...payload} />
					</div>
				);

				return ReactDOM.createPortal(
					component,
					document.getElementById('inner-content'),
				);
			case COMPLETE_SIGNUP_MODAL:
				component = (
					<CompleteSignup close={() => this.handleClose()} {...payload} />
				);
				break;
			case EDIT_FEED_MODAL:
				component = (
					<EditFeedModal close={() => this.handleClose()} {...payload} />
				);
				break;
			default:
				return false;
		}
		/* eslint-enable */

		return (
			<div className={`modal ${(modal || '').toLowerCase()}`}>
				<div ref={this.modalRef} className="modal-content">
					<CloseButton close={() => this.handleClose()} />
					<ErrorBoundary>{component}</ErrorBoundary>
				</div>
			</div>
		);
	}
}

ModalDispatcher.propTypes = {
	modal: PropTypes.string,
	payload: PropTypes.shape({}),
	close: PropTypes.func.isRequired,
	navigationRevert: PropTypes.func.isRequired,
	navigation: PropTypes.shape({
		previous: PropTypes.string,
	}).isRequired,
};

ModalDispatcher.defaultProps = {
	modal: 'CLOSED',
	payload: {},
};

function mapStateToProps({ modal, navigation }) {
	return { ...modal, navigation };
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{ close: hideModal, navigationRevert: setNavigationRevert },
		dispatch,
	);
}

export default connect(mapStateToProps, mapDispatchToProps)(ModalDispatcher);
