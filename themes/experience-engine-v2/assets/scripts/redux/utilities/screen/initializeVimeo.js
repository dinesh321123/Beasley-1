import { loadTritonLibrary } from '../../actions/player';

export default function initializeVimeo() {
	if (window.loadVimeoPlayers) {
		try {
			console.log('Initializing Vimeo Players');
			const numberOfVimeoPlayersOnPage = window.loadVimeoPlayers();

			if (numberOfVimeoPlayersOnPage) {
				loadTritonLibrary();
			}
		} catch {
			console.log('Error while initializing Vimeo Prerolls');
		}
	} else {
		console.log('Vimeo Players NOT configured for prerolls');
	}
}
