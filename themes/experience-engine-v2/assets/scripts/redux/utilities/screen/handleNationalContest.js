export default function handleNationalContest() {
	// check for the existance of an iframe with an id contestframe. if it exists
	// then call a function called createGate and pass it the id of the iframe and the
	// class of the gate which is frame-gate
	if (document.getElementById('contestframe')) {
		// debug code
		console.log('handleNationalContest() - contestframe exists');

		window.removeGate('contestframe');

		// debug code
		console.log('handleNationalContest() - contestframe removed');

		isUserLoggedIn().then(isLoggedIn => {
			if (!isLoggedIn) {
				// debug code
				console.log('handleNationalContest() - isLoggedIn = false');

				window.createGate('contestframe', 'frame-gate');
			} else {
				// debug code
				console.log('handleNationalContest() - isLoggedIn = true');

				window.removeGate('contestframe');
			}
		});
	}
}

function isUserLoggedIn() {
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
