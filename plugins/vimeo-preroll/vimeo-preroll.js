	var vimeoPlayerList;
	window.loadVimeoPlayers = () => {
		vimeoPlayerList = null;
	    console.log('Loading Any Vimeo Player Controls For Embeds')
		const iframeList = Array.from(document.querySelectorAll('iframe'));
		const filteredList = iframeList.filter(iframeElement => iframeElement.src && iframeElement.src.toLowerCase().indexOf('vimeo') > -1);

		if (filteredList && filteredList.length > 0) {
			vimeoPlayerList = filteredList.map(filteredEl => {
				return loadVimeoPlayer(filteredEl)
			});
		}
	}

	const renderHTML = (iFrameElement) => {
		const oldVimeoPrerollWrapper = document.getElementById('vimeoPrerollWrapper');
		if (oldVimeoPrerollWrapper) {
			oldVimeoPrerollWrapper.remove();
		}

		// Hard Coded Mode To Overlay In IFrame - May want option to show full screen in future.
		const renderMode = 'VIMEO WINDOW';
		if (renderMode === 'VIMEO WINDOW') {
			renderVimeoWindowPreroll(iFrameElement)
		} else {
			renderFullScreenPreroll();
		}
	}

	const renderVimeoWindowPreroll = (iFrameElement) => {
		const vimeoPTag = iFrameElement.parentElement;
		vimeoPTag.style.position = 'relative';
		const wrapperDiv = document.createElement('div');
		wrapperDiv.id = 'vimeoPrerollWrapper';
		wrapperDiv.classList.add('preroll-wrapper');
		wrapperDiv.style.position = 'absolute';
		wrapperDiv.style.backgroundColor = 'var(--global-theme-secondary)';
		wrapperDiv.style.height = iFrameElement.style.height;
		wrapperDiv.style.zIndex = '9';
		wrapperDiv.innerHTML = `
			<div id="vimeoPrerollContent" style="height: 0">
				<video id="vimeoPrerollContentElement">
					<track
						src="captions_en.vtt"
						kind="captions"
						srcLang="en"
						label="english_captions"
					/>
				</video>
			</div>
			<div id="vimeoPrerollAdContainer" class="vimeo-preroll-container"/>`;
		vimeoPTag.appendChild(wrapperDiv);
	}

	const renderFullScreenPreroll = () => {
		const containerEl = document.getElementsByClassName('container')[0];
		const wrapperDiv = document.createElement('div');
		wrapperDiv.id = 'vimeoPrerollWrapper';
		wrapperDiv.classList.add('preroll-wrapper');
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
			<div id="vimeoPrerollAdContainer" class="vimeo-preroll-player" />`;
		containerEl.appendChild(wrapperDiv);
	}

	const loadVimeoPlayer = (iFrameElement) => {
		const vimeoplayer = new Vimeo.Player(iFrameElement);
		vimeoplayer.isPlayingPreroll = false;

		vimeoplayer.prerollCallback = async () => {
			if (vimeoplayer.isPlayingPreroll) {
				console.log('Preroll Call Back');
				const wrapperDiv = document.getElementById('vimeoPrerollWrapper');
				wrapperDiv.classList.remove('-active');
				console.log('Vimeo Resumed Play in Callback after Preroll');
				await vimeoplayer.play();
				console.log('Preroll Callback is done!');
				vimeoplayer.isPlayingPreroll = false;
			}
		};

		vimeoplayer.thisVimeoPlayHandler = async () => {
			console.log('Vimeoplayer OnPlay Event');

			if (!vimeoplayer.isPlayingPreroll) {
				vimeoplayer.isPlayingPreroll = true;
				console.log('Played And Instantly Pausing All Players for Preroll');
				// await vimeoplayer.pause();
				await pauseAllVimeoPlayers();
				vimeoplayer.isPlayingPreroll = true; // Reset since it was unset during pause all players
				console.log('Paused and now Playing Preroll');
				/* PREROLL CODE HERE */
				renderHTML(iFrameElement);
				getUrlFromPrebid(vimeoplayer);
			}
		};

		vimeoplayer.on('play', vimeoplayer.thisVimeoPlayHandler);

		vimeoplayer.on('pause', async function () {
			console.log('Paused the video');
		});

		vimeoplayer.getVideoTitle().then(function (title) {
			console.log('title:', title);
		});

		return vimeoplayer;
	}

	const pauseAllVimeoPlayers = () => {
		vimeoPlayerList.map( vp => {
			vp.isPlayingPreroll = false;
			vp.getPaused().then(async function (paused) {
				if (!paused) {
					await vp.pause();
				}
			});
		});
	}

	const getUrlFromPrebid = (vimeoControl) => {
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

		console.log('Setting Pointer To IMA Play Video Func');
		const IMAPlayVimeoIMAAdsFunc = playVimeoIMAAds;

		pbjs.que.push(function () {
			console.log('Removing resetdigital Prebid Ad Unit');
			pbjs.removeAdUnit(gampreroll.unitId);
			console.log('Adding resetdigital Prebid Ad Unit');
			pbjs.addAdUnits(videoAdUnit);

			/*
			pbjs.setConfig({
				cache: {
					url: 'https://prebid.adnxs.com/pbc/v1/cache'
				}
			});
			*/

			console.log('Requesting Vimeo Video Bids');
			pbjs.requestBids({
				timeout: 2000,
				adUnitCodes: [gampreroll.unitId],
				bidsBackHandler: function (bids) {
					console.log(`Preroll Bids Returned:`);
					console.log(JSON.stringify(bids));

					let videoUrl = '';
					if (bids = {}) {
						console.log('No Bids from Prebid');
					} else {
						console.log('Building URL in Prebid');
						videoUrl = pbjs.adServers.dfp.buildVideoUrl({
							adUnit: videoAdUnit,
							params: {
								iu: gampreroll.unitId
							}
						});
						console.log(`URL Returned from Prebid: ${videoUrl}`);
					}

					// If No URL From Prebid, Default to our GAM Unit
					if (!videoUrl) {
						console.log('Using Default GAM Ad Unit for IMA');
						videoUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${gampreroll.unitId}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x360%7C640x480%7C920x508&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;
					}

					try {
						IMAPlayVimeoIMAAdsFunc(videoUrl, vimeoControl);
					} catch(err) {
						console.log('Uncaught Error while playing preroll', err);
						console.log('Attempting to mask error');
						vimeoControl.prerollCallback();
					}
				}
			});
		});
	}


