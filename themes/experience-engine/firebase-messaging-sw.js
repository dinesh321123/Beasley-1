/* eslint-disable no-undef */
// firebase-messaging-sw.js

console.log('FIREBASE MESSAGING SW');

/*
function handleClick (event) {
	event.notification.close();
	// Open the url you set on notification.data
	clients.openWindow(event.notification.data.link_url)
}
*/

if (typeof importScripts === 'function') {
	importScripts(
		'https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js',
	);
	importScripts(
		'https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js',
	);

	const firebaseConfig = {
		apiKey: 'AIzaSyDvXlmIDLb2A65S-ZF9pBpsqwHq6JHDGcg',
		authDomain: 'bbgi-experience-staging.firebaseapp.com',
		databaseURL: 'https://bbgi-experience-staging.firebaseio.com',
		projectId: 'bbgi-experience-staging',
		storageBucket: 'bbgi-experience-staging.appspot.com',
		messagingSenderId: '3738444381',
		appId: '1:3738444381:web:0887af7963e0de2459a3b4',
	};
	firebase.initializeApp(firebaseConfig);

	const messaging = firebase.messaging();
	messaging.onBackgroundMessage(payload => {
		console.log('[firebase-messaging-sw.js] onBackgroundMessage - ', payload);
	});

	/*
	self.removeEventListener('notificationclick', handleClick);
	self.addEventListener('notificationclick', handleClick);
	*/

	console.log('FIREBASE MESSAGING SW LOADED');
}
