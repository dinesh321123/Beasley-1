import amplitudeKit from '@mparticle/web-amplitude-kit';
import mParticle from '@mparticle/web-sdk';

export const setMParticleUserAtributes = (
	email,
	firstname,
	lastname,
	zip,
	gender,
	bday,
) => {
	console.log(
		`Setting mParticle Params: ${email}, ${firstname}, ${lastname}, ${zip}, ${gender}, ${bday}`,
	);

	if (window.firebase?.User) {
		logFirebaseUserIntoMParticle(window.firebase.User);

		const currentUser = mParticle.Identity.getCurrentUser();
		currentUser.setUserAttribute('email', email);
		currentUser.setUserAttribute('$firstname', firstname);
		currentUser.setUserAttribute('$lastname', lastname);
		currentUser.setUserAttribute('$zip', zip);
		currentUser.setUserAttribute('$gender', gender);
		const formattedDob = bday
			.replace(/(\d\d)\/(\d\d)\/(\d{4})/, '$3-$1-$2')
			.split('/')
			.join('-');
		currentUser.setUserAttribute('dob', formattedDob);
	}
};

/**
 * Create MParticle Session if Key is configured and mParticle session has not been created yet
 */
export const createMparticleSession = () => {
	if (
		window.bbgiAnalyticsConfig?.mparticle_key &&
		(!window.mParticle || !window.mParticle.isInitialized())
	) {
		console.log('Configuring mparticle in bundle');

		// eslint-disable-next-line no-undef
		amplitudeKit.register(BeasleyAnalytics.getMParticleConfig());

		// Configures the SDK. Note the settings below for isDevelopmentMode
		// and logLevel.
		mParticle.init(
			window.bbgiAnalyticsConfig.mparticle_key,
			// eslint-disable-next-line no-undef
			BeasleyAnalytics.getMParticleConfig(),
		);
		window.mParticle = mParticle;
		console.log(
			'Done configuring mparticle in bundle, now initializing Beasley Analytics',
		);
		window.beasleyanalytics.initializeMParticle();
	}
};

/**
 * Log Firebase User Into MParticle If Not Done So
 */
export const logFirebaseUserIntoMParticle = firebaseUser => {
	if (!firebaseUser) {
		return;
	}

	createMparticleSession();
	if (window.mParticle && window.mParticle !== {}) {
		const currentUser = window.mParticle.Identity.getCurrentUser();
		if (!currentUser || !currentUser.isLoggedIn()) {
			console.log(
				`Logging Firebase User '${firebaseUser.email}' Into Enabled MParticle Session`,
			);
			const identityRequest = {
				userIdentities: {
					customerid: firebaseUser.email,
					email: firebaseUser.email,
				},
			};
			const identityCallback = result => {
				if (result.getUser()) {
					// proceed with login
					console.log('MPARTICLE LOGIN CALLBACK: ', result);
				}
			};
			window.mParticle.Identity.login(identityRequest, identityCallback);
		}
	}
};

/**
 * Log Firebase User Out Of MParticle If Not Done So
 */
export const logFirebaseUserOutOfMParticle = () => {
	createMparticleSession();
	if (
		window.mParticle &&
		window.mParticle !== {} &&
		window.mParticle.Identity &&
		window.mParticle.Identity.getCurrentUser() &&
		window.mParticle.Identity.getCurrentUser().isLoggedIn()
	) {
		console.log(
			`Logging Out MParticle User '${
				window.mParticle.Identity.getCurrentUser().email
			}' From Enabled MParticle Session`,
		);

		const identityCallback = result => {
			if (result.getUser()) {
				// proceed with login
				console.log('MPARTICLE LOGOUT CALLBACK: ', result);
			}
		};
		window.mParticle.Identity.logout({}, identityCallback);
	}
};