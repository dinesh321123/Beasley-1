import PropTypes from 'prop-types';
import React, { PureComponent } from 'react';

class Close extends PureComponent {
	constructor(props) {
		super(props);

		this.didClick = this.didClick.bind(this);
	}

	render() {
		return (
			<button
				type="button"
				className="button modal-close"
				aria-label="Close"
				onClick={this.didClick}
			>
				<svg
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 212.982 212.982"
					aria-labelledby="close-modal-title close-modal-desc"
					width="13"
					height="13"
				>
					<title id="close-modal-title">Close</title>
					<desc id="close-modal-desc">Checkmark indicating modal close</desc>
					<path
						d="M131.804 106.491l75.936-75.936c6.99-6.99 6.99-18.323 0-25.312-6.99-6.99-18.322-6.99-25.312 0L106.491 81.18 30.554 5.242c-6.99-6.99-18.322-6.99-25.312 0-6.989 6.99-6.989 18.323 0 25.312l75.937 75.936-75.937 75.937c-6.989 6.99-6.989 18.323 0 25.312 6.99 6.99 18.322 6.99 25.312 0l75.937-75.937 75.937 75.937c6.989 6.99 18.322 6.99 25.312 0 6.99-6.99 6.99-18.322 0-25.312l-75.936-75.936z"
						fillRule="evenodd"
						clipRule="evenodd"
					/>
				</svg>
			</button>
		);
	}

	isUserLoggedIn() {
		return new Promise((resolve, reject) => {
			window.firebase.auth().onAuthStateChanged(user => {
				if (user) {
					resolve(true);
				} else {
					resolve(false);
				}
			});
		});
	}

	removeContestGating() {
		// debug code
		console.log('removeContestGating()');

		if (document.getElementById('contestframe')) {
			// debug code
			console.log('removeContestGating() - contestframe exists');

			window.removeGate('contestframe');
			this.isUserLoggedIn().then(isLoggedIn => {
				// debug code
				console.log(`removeContestGating() - isLoggedIn = ${isLoggedIn}`);

				if (!isLoggedIn) {
					// debug code
					console.log('removeContestGating() - isLoggedIn = false');

					window.createGate('contestframe', 'frame-gate');
				} else {
					// debug code
					console.log('removeContestGating() - isLoggedIn = true');

					window.removeGate('contestframe');
				}
			});
		}
	}

	didClick() {
		// debug code
		// console.log('didClick()');

		const beforeClose = window.beforeBeasleyModalClose;
		const { close } = this.props;

		// debug code
		// console.log(`didClick() - beforeClose = ${beforeClose}`);

		if (beforeClose) {
			const result = beforeClose();

			// debug code
			// console.log(`didClick() - result = ${result}`);

			if (result) {
				if (close) {
					// debug code
					// console.log('didClick() - close()');

					close();

					// debug code
					// console.log('didClick() - removeContestGating()');

					// this.removeContestGating();
				}
			}
		} else if (close) {
			close();
			// this.removeContestGating();
		}
	}
}

Close.propTypes = {
	close: PropTypes.func.isRequired,
};

export default Close;
