import firebase from 'firebase/compat/app';
import 'firebase/compat/auth';
import 'firebase/compat/messaging';

const { firebase: config, vapidKey } = window.bbgiconfig;

firebase.initializeApp(config);

const firebaseAuth = firebase.auth();
window.firebase = firebase;

const initializeWebPush = () => {
	const firebaseMessaging = firebase.messaging();
	navigator.serviceWorker
		.register(
			`/firebase-messaging-sw.js?firebaseConfig=${encodeURIComponent(
				JSON.stringify(config),
			)}`,
		)
		.then(swRegistration => {
			// Get registration token. Initially this makes a network call, once retrieved
			// subsequent calls to getToken will return from cache.
			firebaseMessaging
				.getToken({
					vapidKey,
					serviceWorkerRegistration: swRegistration,
				})
				.then(currentToken => {
					if (currentToken) {
						console.log(`FOUND TOKEN - '${currentToken}'`);
						firebaseMessaging.onMessage(messageHandler);

						// Register the token to the Beasley WebUser topic
						const { id: channel } = window.bbgiconfig.publisher;
						fetch(
							`${
								window.bbgiconfig.eeapi
							}experience/channels/${channel}/webusertopic/${encodeURIComponent(
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
		});
};

const messageHandler = payload => {
	console.log('[firebase.js] onMessage - ', payload);

	// Exit if Nofifications are not supported as in the case of Chrome for Android.
	if (navigator.userAgent.indexOf('Android') > -1) {
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
			picture && picture.original ? picture.original.url : defaultImageUrl;
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
};

// Init Web Push If Vars Are Configured
if (config.appId && config.messagingSenderId && vapidKey) {
	initializeWebPush();
} else {
	console.log(`Could Not Initialize Web Push. Settings are incomplete`);
}

export { firebase, firebaseAuth };
