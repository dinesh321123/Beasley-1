export default function initializeVimeo() {
	try {
		window.loadVimeoPlayers();
		console.log('Vimeo Players Initialized');
	} catch {
		console.log('Vimeo Players NOT Initialized');
	}
}
