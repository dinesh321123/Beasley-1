/* eslint-disable no-undef */
// firebase-messaging-sw.js

console.log('FIREBASE MESSAGING SW');

if (typeof importScripts === 'function') {
	importScripts(
		'https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js',
	);
	importScripts(
		'https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js',
	);

	const firebaseConfigString = new URL(location.toString()).searchParams.get(
		'firebaseConfig',
	);
	const firebaseConfig = JSON.parse(firebaseConfigString);
	console.log(
		`Service Worker Received firebaseConfig - ${JSON.stringify(
			firebaseConfig,
		)}`,
	);

	firebase.initializeApp(firebaseConfig);

	const messaging = firebase.messaging();
	messaging.onBackgroundMessage(payload => {
		console.log('[firebase-messaging-sw.js] onBackgroundMessage - ', payload);
	});
}

console.log('FIREBASE MESSAGING SW EVALUATED');
