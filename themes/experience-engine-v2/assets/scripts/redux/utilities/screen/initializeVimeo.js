export default function initializeVimeo() {
	if (window.loadVimeoPlayers) {
		try {
			window.loadVimeoPlayers();
			console.log('Vimeo Players Initialized for Prerolls');
		} catch {
			console.log('Error while initializing Vimeo Prerolls');
		}
	} else {
		console.log('Vimeo Players NOT configured for prerolls');
	}
}
