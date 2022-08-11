export default function initializeVimeo() {
	if (window.loadVimeoPlayers) {
		try {
			window.loadVimeoPlayers();
		} catch (err) {
			console.log('Error while initializing Vimeo Prerolls ', err.message);
		}
	} else {
		console.log('Vimeo Players NOT configured for prerolls');
	}
}
