export default function initializeVimeo(shouldKeepPriorVimeoPlayers) {
	if (window.loadVimeoPlayers) {
		try {
			window.loadVimeoPlayers(shouldKeepPriorVimeoPlayers);
		} catch (err) {
			console.log('Error while initializing Vimeo Prerolls ', err.message);
		}
	} else {
		console.log('Vimeo Players NOT configured for prerolls');
	}
}
