import React from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { showSignUpModal } from '../redux/actions/modal';
import { readBID } from '../redux/actions/uniqueuserid';
import { uniqueUserId } from '../library/uniqueuserid';

class UniqueUserid extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
		this.onClick = this.handleClick.bind(this);
		// console.log('Console handleClick event construc: ', this.onClick);
	}

	handleClick(e) {
		const { target } = e;
		console.log('Console handleClick event from UniqueUserid.js', target);
	}

	componentDidMount() {
		// this.props.showSignIn();
		uniqueUserId();
		this.props.bidValueActionCall('pass the value into the function');
		// console.log('didmount BID', this.props.newBIDValue);
	}

	render() {
		// console.log('Render BID', this.props.newBIDValue);
		// const data = `<div><div>BID Valaue: ${this.props.bidCookies}/n</div><div>BID Created: ${this.props.bidCreated}</div><div>BID Current Session: ${this.props.bidCurrentSession}</div><div>BID Prior Session: ${this.props.bidPriorSession}</div></div>`;
		// return (ReactDOM.createPortal(data, document.getElementById('footer')));
		return ReactDOM.createPortal(
			<div className="cookiesvalue">
				<div>BID Valaue: {this.props.bidCookies}</div>
				<div>BID Created: {this.props.bidCreated}</div>
				<div>BID Current Session: {this.props.bidCurrentSession}</div>
				<div>BID Prior Session: {this.props.bidPriorSession}</div>
			</div>,
			document.getElementById('footer'),
		);
	}
}

UniqueUserid.propTypes = {
	// showSignIn: PropTypes.func.isRequired,
	bidValueActionCall: PropTypes.func.isRequired,
	bidCookies: PropTypes.string,
	bidCreated: PropTypes.node,
	bidCurrentSession: PropTypes.node,
	bidPriorSession: PropTypes.node,
};
UniqueUserid.defaultProps = {
	bidCookies: '',
	bidCreated: '',
	bidCurrentSession: '',
	bidPriorSession: '',
};

function mapStateToProps({ uniqueuserid }) {
	// console.log('mapStateToProps: ', uniqueuserid);
	return {
		bidCookies: uniqueuserid.BID,
		bidCreated: uniqueuserid.BID_CREATED,
		bidCurrentSession: uniqueuserid.CURRENT_SESSION,
		bidPriorSession: uniqueuserid.PRIOR_SESSION,
	};
}
function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			showSignIn: showSignUpModal,
			bidValueActionCall: readBID,
		},
		dispatch,
	);
}
export default connect(mapStateToProps, mapDispatchToProps)(UniqueUserid);
