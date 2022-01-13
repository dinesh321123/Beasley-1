// import * as firebase from 'firebase/app';
import firebase from 'firebase/compat/app';

// Add the Firebase products that you want to use
// import 'firebase/auth';
// import 'firebase/messaging';
import 'firebase/compat/auth';
import 'firebase/compat/messaging';

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
			console.log(`FOUND TOKEN - '${currentToken}'`);
			firebaseMessaging.onMessage(payload => {
				console.log('[firebase.js] onMessage - ', payload);

				// Exit if Nofifications are not supported as in the case of Chrome for Android.
				if (navigator.userAgent.indexOf('android') > -1) {
					console.log(`Exiting because Notifications are not supported.`);
					return;
				}
				console.log(
					`Preparing to show Notification for User Agent - ${navigator.userAgent}.`,
				);
				const title =
					payload.notification && payload.notification.title
						? `FOREGROUND: ${payload.notification.title}`
						: 'Beasley Media';

				let defaultImageUrl = '';
				const { streams } = window.bbgiconfig;
				if (streams && streams.length > 0) {
					const { picture } = streams[0];
					defaultImageUrl =
						picture && picture.original
							? picture.original.url
							: defaultImageUrl;
				}

				console.log(`Default IMAGE: ${defaultImageUrl}`);
				const imageUrl =
					payload.notification && payload.notification.image
						? payload.notification.image
						: defaultImageUrl;
				const body =
					payload.notification && payload.notification.body
						? payload.notification.body
						: 'Notification';
				const link_url =
					payload.data && payload.data.link_url ? payload.data.link_url : '';
				const notification = new Notification(title, {
					body,
					icon: imageUrl,
					click_action: link_url,
				});
				notification.onclick = () => {
					window.alert('Yep');
				};
			});

			// Register the token to the WebUsers topic
			const { id: channel } = window.bbgiconfig.publisher;
			fetch(
				`${
					window.bbgiconfig.eeapi
				}experience/channels/${channel}/webuser/${encodeURIComponent(
					currentToken,
				)}`,
				{
					method: 'POST',
				},
			);
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

export { firebase, firebaseAuth };
