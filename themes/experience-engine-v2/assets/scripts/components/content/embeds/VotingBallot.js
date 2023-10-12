import React, { Component } from 'react';
import PropTypes from 'prop-types';
/**
 * VotingBallot Component
 */
class VotingBallot extends Component {
	/**
	 * componentDidMount
	 */
	componentDidMount() {
		const { contestcode } = this.props;
		console.log('contestcode:', contestcode);
		const src_element = `https://voteei.com/${contestcode}`;
		const iframe = document.createElement('iframe');
		iframe.src = src_element; // Set the URL you want to load
		iframe.width = '600px'; // Set the width of the iframe
		iframe.height = '1200px';
		iframe.frameBorder = '0';
		iframe.allowFullscreen = 'true';
		const parentElement = document.getElementById('eie_app');
		parentElement.appendChild(iframe);
	}

	/**
	 * componentWillUnmount
	 * This method is called when the component is about to be removed from the DOM.
	 */
	componentWillUnmount() {}

	/**
	 * render
	 */
	render() {
		const { contestcode } = this.props;
		return <div id="eie_app" className={contestcode} />;
	}
}
VotingBallot.propTypes = {
	contestcode: PropTypes.string, // Specifies that someProp should be a string and is required
};

VotingBallot.defaultProps = {
	contestcode: '',
};
export default VotingBallot;
