	window.loadVimeoPlayers = () => {
		const iframeList = Array.from(document.querySelectorAll('iframe'));
		const filteredList = iframeList.filter(iframeElement => iframeElement.src && iframeElement.src.toLowerCase().indexOf('vimeo') > -1);

		if (filteredList.length > 0) {
			renderHTML();

			filteredList.map(filteredEl => {
				return loadVimeoPlayer(filteredEl)
			});
		}
	}

	const renderHTML = () => {
		const containerEl = document.getElementsByClassName('container')[0]
		const wrapperDiv = document.createElement('div');
		wrapperDiv.id = 'vimeoPrerollWrapper';
		wrapperDiv.classList.add(['gamPreroll-wrapper', 'active']);
		wrapperDiv.innerHTML = `
			<div id="vimeoPrerollContent">
				<video id="vimeoPrerollContentElement">
					<track
						src="captions_en.vtt"
						kind="captions"
						srcLang="en"
						label="english_captions"
					/>
				</video>
			</div>
			<div id="vimeoPrerollAdContainer" className="gam-preroll-player" />`;
		containerEl.appendChild(wrapperDiv);
	}

	const loadVimeoPlayer = (iframe) => {
		const vimeoplayer = new Vimeo.Player(iframe);
		let isPlayingPreroll = false;

		const prerollCallback = async () => {
			await vimeoplayer.play();
			isPlayingPreroll = false;
			console.log('Vimeo Playing');
		}

		vimeoplayer.on('play', async function () {
			console.log('Attempting to Play the video');

			if (!isPlayingPreroll) {
				isPlayingPreroll = true;
				console.log('Played And Instantly Pausing');
				await vimeoplayer.pause();
				console.log('Paused and now Playing Preroll');
				/* PREROLL CODE HERE */
				getUrlFromPrebid(prerollCallback);
			}
		});

		vimeoplayer.on('pause', async function () {
			console.log('Paused the video');
		});

		vimeoplayer.getVideoTitle().then(function (title) {
			console.log('title:', title);
		});
	}

	const getUrlFromPrebid = (prerollCallback) => {
		const {gampreroll} = window.bbgiconfig.dfp;
		const videoAdUnit = {
			code: gampreroll.unitId,
			mediaTypes: {
				video: {
					playerSize: [[640, 360]],
					context: 'instream'
				}
			},
			bids: [{
				bidder: 'resetdigital',
				params: {
					pubId: '44',
				},
			}]
		};

		pbjs.que.push(function () {
			pbjs.removeAdUnit(gampreroll.unitId);
			pbjs.addAdUnits(videoAdUnit);


			/*
			pbjs.setConfig({
				cache: {
					url: 'https://prebid.adnxs.com/pbc/v1/cache'
				}
			});
			*/

			pbjs.requestBids({
				timeout: 2000,
				adUnitCodes: [gampreroll.unitId],
				bidsBackHandler: function (bids) {
					console.log(`Preroll Bids Returned:`);
					console.log(JSON.stringify(bids));

					// TODO - Replace mock with actual callouts. This is only for placeholder until we get data back from Prebid

					//if (bids = {}) {
					//	prerollCallback();
					//} else {
					//	const videoUrl = pbjs.adServers.dfp.buildVideoUrl({
					//		adUnit: videoAdUnit,
					//		params: {
					//			iu: gampreroll.unitId
					//		}
					//	});
					const videoUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${gampreroll.unitId}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x360%7C640x480%7C920x508&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;
						console.log(videoUrl);
						if (videoUrl) {
							//try {
								playVimeoIMAAds(videoUrl, prerollCallback);
							//} catch {
							//	prerollCallback();
							//}
						} else {
							prerollCallback();
						}
					//}
				}
			});
		});
	}


