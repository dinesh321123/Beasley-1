	console.log('Vimeo-preroll loading.')

	const loadVimeoPlayers = () => {
		const iframeList = Array.from(document.querySelectorAll('iframe'));
		iframeList.filter(iframeElement => iframeElement.src && iframeElement.src.toLowerCase().indexOf('vimeo') > -1)
				  .map(filteredEl => {
				  	return loadVimeoPlayer(filteredEl)
				  });
	}

	const loadVimeoPlayer = (iframe) => {
		const vimeoplayer = new Vimeo.Player(iframe);
		let isPlayingPreroll = false;

		vimeoplayer.on('play', async function () {
			console.log('Attempted to Play the video');

			if (!isPlayingPreroll) {
				isPlayingPreroll = true;
				console.log('Played And Instantly Pausing');
				await vimeoplayer.pause();
				console.log('Paused and now Playing Preroll');
				/* PREROLL CODE HERE */
				// For POC, fake preroll with a 5 second delay...
				setTimeout(async () => {
					await vimeoplayer.play();
					isPlayingPreroll = false;
					console.log('Vimeo Playing');
				}, 5000);
			}
		});

		vimeoplayer.on('pause', async function () {
			console.log('Paused the video');
		});

		vimeoplayer.getVideoTitle().then(function (title) {
			console.log('title:', title);
		});
	}

	const oldOnload=window.onload;
	window.onload=function(){
		oldOnload && oldOnload();
		loadVimeoPlayers();
		console.log('Vimeo-preroll loaded.')
	}


