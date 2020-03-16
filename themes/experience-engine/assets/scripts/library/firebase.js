import * as firebase from 'firebase/app';

// Add the Firebase products that you want to use
import 'firebase/auth';

// TODO: expose this through WordPress (wp_localize_script) and not the window globals.
const { firebase: config } = window.bbgiconfig;

firebase.initializeApp(config);

const firebaseAuth = firebase.auth();

export { firebase, firebaseAuth };
