export default function initializeVimeo() {
	console.log('Setting timeout for vimeo ima setup');
	setTimeout(() => {
		window.loadVimeoPlayers();
		window.setUpVimeoIMA();
	}, 2000);

	console.log('Set timeout for initializeVimeo()');
}
