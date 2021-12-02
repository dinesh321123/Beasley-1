	const VIMEOPREROLLWRAPPER = 'vimeoPrerollWrapper';
	var vimeoPlayerList;
	window.loadVimeoPlayers = () => {
		vimeoPlayerList = null;
		const { bbgiconfig } = window;

		if (!bbgiconfig.prebid_enabled) {
			console.log('Error: PREBID not enabled - CANNOT LOAD VIMEO PREROLLS');
			return;
		}

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
		const oldVimeoPrerollWrapper = document.getElementById(VIMEOPREROLLWRAPPER);
		if (oldVimeoPrerollWrapper) {
			oldVimeoPrerollWrapper.remove();
		}

		//  TODO - This likely only works on Chrome. When time permits, test and support all other browsers.
		if (document.fullscreenElement) {
			renderFullScreenPreroll(iFrameElement);
		} else {
			renderVimeoPreroll(iFrameElement);
		}
	}

	const getVimeoInnerHTML = (shouldAddFullScreenPlayerStyle) => {
		return `<div id="vimeoPrerollContent" style="height: 0">
			<video id="vimeoVideoElement">
				<track
					src="captions_en.vtt"
					kind="captions"
					srcLang="en"
					label="english_captions"
				/>
			</video>
		</div>
		<div id="vimeoPrerollAdContainer" ${shouldAddFullScreenPlayerStyle ? 'class="gam-preroll-player"' : ''} />`;
	}

	const renderVimeoPreroll = (iFrameElement) => {
		const vimeoPTag = iFrameElement.parentElement;
		vimeoPTag.style.position = 'relative';
		const wrapperDiv = document.createElement('div');
		wrapperDiv.id = VIMEOPREROLLWRAPPER;
		wrapperDiv.classList.add('preroll-wrapper');
		wrapperDiv.style.position = 'absolute';
		wrapperDiv.style.backgroundColor = 'white';
		wrapperDiv.style.height = iFrameElement.style.height;
		wrapperDiv.style.zIndex = '9';
		wrapperDiv.innerHTML = getVimeoInnerHTML(false);
		vimeoPTag.appendChild(wrapperDiv);
	}

	const renderFullScreenPreroll = (iFrameElement) => {
		// Add Full black screen because exiting full screen mode briefly shows html page.
		const fullscreenShade = document.createElement('div');
		fullscreenShade.classList.add('preroll-wrapper');
		fullscreenShade.style.backgroundColor = 'var(--global-black);'
		fullscreenShade.style.display = 'block';
		document.documentElement.appendChild(fullscreenShade);

		const wrapperDiv = document.createElement('div');
		wrapperDiv.id = VIMEOPREROLLWRAPPER;
		wrapperDiv.classList.add('preroll-wrapper');
		wrapperDiv.style.backgroundColor = 'black';
		wrapperDiv.style.zIndex = '1000';
		wrapperDiv.innerHTML = getVimeoInnerHTML(true);

		iFrameElement.parentElement.appendChild(wrapperDiv);

		document.exitFullscreen().then(async () => {
			await iFrameElement.parentElement.requestFullscreen();
			// Remove the full black screen.
			fullscreenShade.remove();
		});
	}

	const loadVimeoPlayer = (iFrameElement) => {
		// Add Class to parent for Full Screen
	    iFrameElement.parentElement.classList.add('beasley-vimeo');

		const vimeoplayer = new Vimeo.Player(iFrameElement);
		vimeoplayer.isPlayingPreroll = false;

		vimeoplayer.prerollCallback = async () => {
			if (vimeoplayer.isPlayingPreroll) {
				console.log('Preroll Call Back');
				const wrapperDiv = document.getElementById(VIMEOPREROLLWRAPPER);
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
				await vimeoplayer.pause();
				await pauseAllVimeoPlayers();
				vimeoplayer.isPlayingPreroll = true; // Reset since it was unset during pause all players
				console.log('Paused and now Playing Preroll');
				/* PREROLL CODE HERE */
				await sendGAPlayEvent();
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

	const sendGAPlayEvent = async () => {
		const {global} = window.bbgiconfig.dfp;
		// global holds a 2 dimensional array like "global":[["cdomain","wmmr.com"],["cpage","home"],["ctest",""],["genre","rock"],["market","philadelphia, pa"]]
		const videoID = await vimeoControl.getVideoId();
		const globalObj = global.reduce((acc, item) => {
			const key = `${item[0]}`;
			acc[key] = `${item[1]}`;
			return acc;
		}, {});

		try {
			window.ga('send', {
				hitType: 'event',
				eventCategory: 'VimeoPlay',
				eventAction: `${globalObj.cdomain}`,
				eventLabel: `${videoID}`,
			});
		} catch (ex) {
			console.log(`ERROR Sending Vimeo Play Event to Google Analytics: `, ex);
		}
	}

	const pauseAllVimeoPlayers = async () => {
		await Promise.all(vimeoPlayerList.map(vp => {
			vp.isPlayingPreroll = false;
			vp.getPaused().then(async function (paused) {
				if (!paused) {
					return vp.pause();
				} else {
					return null;
				}
			});
		}));

	}

	const getUrlFromPrebid = (vimeoControl) => {
		const { global, incontentpreroll } = window.bbgiconfig.dfp;

		if  (!incontentpreroll || !incontentpreroll.unitId) {
			console.log(`Not playing Vimeo preroll because no incontentpreroll.unitId was  found.`);
			vimeoControl.prerollCallback();
			return;
		}

		const videoAdUnit = {
			code: incontentpreroll.unitId,
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
			pbjs.removeAdUnit(incontentpreroll.unitId);
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
				adUnitCodes: [incontentpreroll.unitId],
				bidsBackHandler: async function (bids) {
					console.log(`Preroll Bids Returned:`);
					console.log(JSON.stringify(bids));

					let videoUrl = '';
					// NOTE: Bids Are Ignored. Change below to == in order to enable Prebid.
					if (bids = {}) {
						console.log('No Bids from Prebid');
					} else {
						console.log('Building URL in Prebid');
						videoUrl = pbjs.adServers.dfp.buildVideoUrl({
							adUnit: videoAdUnit,
							params: {
								iu: incontentpreroll.unitId
							}
						});
						console.log(`URL Returned from Prebid: ${videoUrl}`);
					}

					// If No URL From Prebid, Default to our GAM Unit
					if (!videoUrl) {
						console.log('Using Default GAM Ad Unit for IMA');
						const videoID = await vimeoControl.getVideoId();
						console.log(`Video ID is ${videoID}`);

						const partialCustParamsString = `&cust_params=VimeoVideoID%3D${videoID}`;
						// global holds a 2 dimensional array like "global":[["cdomain","wmmr.com"],["cpage","home"],["ctest",""],["genre","rock"],["market","philadelphia, pa"]]
						mappedGlobalParamArray = global.map(innerArray => {
							return `%26${innerArray[0]}%3D${innerArray[1]}`;
						});
						const fullCustParamsString = partialCustParamsString.concat(mappedGlobalParamArray);
						// videoUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${incontentpreroll.unitId}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x360%7C640x480%7C920x508&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;
						videoUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${incontentpreroll.unitId}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x360${fullCustParamsString}&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;
					}

					try {
						IMAPlayVimeoIMAAdsFunc(videoUrl, vimeoControl);
					} catch (err) {
						console.log('Uncaught Error while playing preroll', err);
						console.log('Attempting to mask error');
						vimeoControl.prerollCallback();
					}
				}
			});
		});
	}


