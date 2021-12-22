import * as firebase from 'firebase/app';

// Add the Firebase products that you want to use
import 'firebase/auth';
import 'firebase/messaging';

// TODO: expose this through WordPress (wp_localize_script) and not the window globals.
const { firebase: config } = window.bbgiconfig;

/*
// Firebase Configuration For WRIF Staging Web
const firebaseConfig = {
  apiKey: "AIzaSyDvXlmIDLb2A65S-ZF9pBpsqwHq6JHDGcg",
  authDomain: "bbgi-experience-staging.firebaseapp.com",
  databaseURL: "https://bbgi-experience-staging.firebaseio.com",
  projectId: "bbgi-experience-staging",
  storageBucket: "bbgi-experience-staging.appspot.com",
  messagingSenderId: "3738444381",
  appId: "1:3738444381:web:0887af7963e0de2459a3b4"
};
 */

// Mockup for Testing - TODO - Add New Settings To EE
config.appId = '1:3738444381:web:0887af7963e0de2459a3b4';
config.messagingSenderId = '3738444381';

firebase.initializeApp(config);

const firebaseAuth = firebase.auth();
const firebaseMessaging = firebase.messaging();
window.firebase = firebase;

// Get registration token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
firebaseMessaging
	.getToken({
		vapidKey:
			'BF3ajZ0tXj9Y4TsWcl2y-MpKTLxZcptUshawzQIGeg3UHZbpc-TGCtQNK39wsAoHDbntRb7ssUYa2k6hPuK4BXI',
	})
	.then(currentToken => {
		if (currentToken) {
			// Send the token to your server and update the UI if necessary
			// ...
			console.log(`FOUND TOKEN`);
		} else {
			// Show permission request UI
			console.log(
				'No registration token available. Request permission to generate one.',
			);
			// ...
		}
	})
	.catch(err => {
		console.log('An error occurred while retrieving token. ', err);
		// ...
	});

export { firebase, firebaseAuth, firebaseMessaging };
