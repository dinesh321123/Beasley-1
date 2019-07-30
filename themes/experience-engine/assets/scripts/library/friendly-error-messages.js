export function mapAuthErrorCodeToFriendlyMessage( error ) {
	switch( error.code ) {
		case 'auth/user-disabled':
			return 'Whoops! Your user account has been disabled. Please register a new account if you wish to sign in.';
			break;
		case 'auth/user-not-found':
			return 'Whoops! Your user account was not found. Please register a new account if you wish to sign in.';
			break;
		case 'auth/wrong-password':
			return 'Whoops! The password you entered was incorrect. Please try again or click Forgot Password below to reset it.';
			break;
		case 'auth/email-already-in-use':
			return 'We are sorry, but an account with that email address already exists. Sign in or use Forgot Password to reset your password.';
			break;
		case 'auth/invalid-email':
			return 'Whoops! You have not entered a valid email address.';
			break;
		case 'auth/operation-not-allowed':
			return 'Whoops! Registration using email accounts has been disabled on this site. Please use a social login.';
			break;
		case 'auth/weak-password':
			return 'Whoops! Your password must be 6 characters or longer.';
			break;
		default:
			return 'Whoops! We are sorry, but there has been an error we didn\'t expect.  Please try again soon.';
	}
}
