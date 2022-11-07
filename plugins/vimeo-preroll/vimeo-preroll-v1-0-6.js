/*** Any Change To This File Must Entail A Version Change Due To Perma-Cache ***/
/*** Includes are held in vimeo-preroll.php ***/
/* v1-0-0 - Institute Versioning to comply with permacache */
/* v1-0-2 - Lazy Load Google IMA */
/* v1-0-4 - Put in proxy button to convince IOS IMA that user action initiated Ad Play */

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
		const filteredList = iframeList.filter(
			iframeElement => iframeElement.src &&
			iframeElement.src.toLowerCase().indexOf('vimeo') > -1 &&
			iframeElement.src.indexOf('?s=') === -1
		);

		if (filteredList && filteredList.length > 0) {
			loadIMALibrary();
			vimeoPlayerList = filteredList.map(filteredEl => {
				return loadVimeoPlayer(filteredEl)
			});
		}
	}

	const loadIMALibrary = () => {
		// Exit if already loaded
		if (window.google && window.google.ima) {
			return;
		}

		const imaLibElementName = 'imaLibElement';
		if (!document.getElementById(imaLibElementName)) {
			console.log('Loading IMA Library');
			const imaIncludeScript = document.createElement('script');
			imaIncludeScript.setAttribute('id', imaLibElementName);
			imaIncludeScript.setAttribute('async', true);
			document.body.appendChild(imaIncludeScript);
			imaIncludeScript.setAttribute(
				'src',
				'https://imasdk.googleapis.com/js/sdkloader/ima3.js',
			);
		}
	}

	const renderHTML = (iFrameElement) => {
		const oldVimeoPrerollWrapper = document.getElementById(VIMEOPREROLLWRAPPER);
		if (oldVimeoPrerollWrapper) {
			oldVimeoPrerollWrapper.remove();
		}

		//  TODO - This likely only works on Chrome. When time permits, test and support all other browsers.
		if (document.fullscreenElement && !isIOS()) {
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

	const getVimeoInnerHTMLForIOS = (vimeoIFrameElement) => {
		return `<div id="vimeoPrerollContent" style="height: 0">
				<video id="vimeoVideoElement" width="${vimeoIFrameElement.clientWidth}" height="${vimeoIFrameElement.clientHeight}" playsInline>
					<track
						src="captions_en.vtt"
						kind="captions"
						srcLang="en"
						label="english_captions"
					/>
				</video>
			</div>
			<div id="vimeoPrerollAdContainer" />`;
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
		if (isIOS()) {
			wrapperDiv.innerHTML = getVimeoInnerHTMLForIOS(iFrameElement);
		} else {
			wrapperDiv.innerHTML = getVimeoInnerHTML(false);
		}
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

	// TODO - Determining OS should be single function in single place for entire App.
	const isIPhone = () => {
		const { userAgent } = window.navigator;
		return !!userAgent.match(/iPhone/i);
	};

	const isIPad = () => {
		const { userAgent } = window.navigator;

		return (
			!!userAgent.match(/iPad/i) ||
			(!!userAgent.match(/Mac/i) &&
				'ontouchend' in document) /* iPad OS 13+ in default desktop mode */
		);
	};

	const isIOS = () => {
		return isIPhone() || isIPad();
	};

	const getVimeoPlayerForIOS = (iFrameElement) => {
		console.log('Creating Extra HTML for IOS');
		// Add allow=autoplay to Vimeo IFrame so that play button can interact.
		// Swap out with original - Chrome did not work when original was modified.
		// Wrap copy in div which onmouseover inits IMA.
		const newDivElement = document.createElement('div');
		newDivElement.setAttribute(
			'style',
			'position: relative',
		);
		const newIFrameElement = iFrameElement.cloneNode(true);
		newIFrameElement.setAttribute('allow', 'autoplay; fullscreen');

		// .responsive class was causing 0 height style - override style with iframe height attribute
		const heightVal = newIFrameElement.getAttribute('height');
		console.log(`Setting Vimeo IFrame Style Height: ${heightVal}`);
		newIFrameElement.setAttribute('style', `height: ${heightVal ? heightVal : 0}px`);

		newDivElement.appendChild(newIFrameElement);
		iFrameElement.parentNode.replaceChild(newDivElement, iFrameElement);

		// On IOS, IMA does not consider Vimeo Events as User Interaction.
		// Create a button to use as a proxy click event.
		const trickIMAButton = document.createElement("div");
		trickIMAButton.setAttribute(
			'style',
			'position: absolute; bottom: 0; left: 0; width: 25%; height: 50%;',
		);

		newDivElement.appendChild(trickIMAButton);
		const retval = new Vimeo.Player(newIFrameElement);

		trickIMAButton.onclick = () => {
			console.log('DEBUG BUTTON CLICK');
			renderHTML(newIFrameElement);
			createIMADisplayContainer();
			retval.play();
			trickIMAButton.remove(); // Delete trick button since we already played IMA Ad
		}

		return retval;
	}

	const loadVimeoPlayer = (iFrameElement) => {
		// Add Class to parent for Full Screen
	    iFrameElement.parentElement.classList.add('beasley-vimeo');

		// Add Class to parent to avoid padding added by .responsive classed on some pages
		iFrameElement.parentElement.classList.add('beasley');

		const vimeoplayer = isIOS() ? getVimeoPlayerForIOS(iFrameElement) : new Vimeo.Player(iFrameElement);

		vimeoplayer.isPlayingPreroll = false;
		vimeoplayer.finishedPlayingPreroll = false;

		vimeoplayer.prerollCallback = async () => {
			if (vimeoplayer.isPlayingPreroll) {
				console.log('Preroll Call Back');
				const wrapperDiv = document.getElementById(VIMEOPREROLLWRAPPER);
				if (wrapperDiv) {
					wrapperDiv.remove();
				}

				console.log('Vimeo Resumed Play in Callback after Preroll');
				await vimeoplayer.play();
				console.log('Preroll Callback is done!');
				vimeoplayer.isPlayingPreroll = false;
				vimeoplayer.finishedPlayingPreroll = true;
			}
		};

		vimeoplayer.thisVimeoPlayHandler = async () => {
			console.log('Vimeoplayer OnPlay Handler');

			// Play preroll if we are currently not playing preroll and have not already finished playing preroll.
			if (!vimeoplayer.isPlayingPreroll && !vimeoplayer.finishedPlayingPreroll) {
				vimeoplayer.isPlayingPreroll = true;
				console.log('Played And Instantly Pausing All Players for Preroll');
				await vimeoplayer.pause();
				await pauseAllVimeoPlayers();
				vimeoplayer.isPlayingPreroll = true; // Reset since it was unset during pause all players
				console.log('Paused and now Playing Preroll');
				/* PREROLL CODE HERE */
				if (! document.getElementById(VIMEOPREROLLWRAPPER)) {
					renderHTML(iFrameElement);
					createIMADisplayContainer();
				}
				await getUrlFromPrebid(vimeoplayer);
			}
		};

		vimeoplayer.on('play', () => {
			vimeoplayer.thisVimeoPlayHandler();
		});

		vimeoplayer.on('pause', async function () {
			console.log('Paused the video');
		});

		vimeoplayer.getVideoTitle().then(function (title) {
			console.log('title:', title);
		});

		return vimeoplayer;
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

	const getUrlFromPrebid = async (vimeoControl) => {
		const { global, incontentpreroll } = window.bbgiconfig.dfp;

		if  (!incontentpreroll || !incontentpreroll.unitId) {
			console.log(`Not playing Vimeo preroll because no incontentpreroll.unitId was  found.`);
			vimeoControl.prerollCallback();
			return;
		}

		/* 01/05/2022 - Disable Reset Digital
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
			// Stub to set cache in event there are ever stutter problems
			// pbjs.setConfig({
			// cache: {
			//		url: 'https://prebid.adnxs.com/pbc/v1/cache'
			//	 }
			// });

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
						const mappedGlobalParamArray = global.map(innerArray => {
							return `%26${innerArray[0]}%3D${innerArray[1]}`;
						});
						const mappedGlobalParamString = mappedGlobalParamArray ? mappedGlobalParamArray.join('') : '';
						const fullCustParamsString = partialCustParamsString.concat(mappedGlobalParamString);
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
		*/

		/* 01/05/2022 - Call Default GAM Ad Unit Since We Disabled Reset Digital Above */
		const videoID = await vimeoControl.getVideoId();
		console.log(`Video ID is ${videoID}`);

		const partialCustParamsString = `&cust_params=VimeoVideoID%3D${videoID}`;
		// global holds a 2 dimensional array like "global":[["cdomain","wmmr.com"],["cpage","home"],["ctest",""],["genre","rock"],["market","philadelphia, pa"]]
		const mappedGlobalParamArray = global.map(innerArray => {
			return `%26${innerArray[0]}%3D${innerArray[1]}`;
		});
		const mappedGlobalParamString = mappedGlobalParamArray ? mappedGlobalParamArray.join('') : '';
		const fullCustParamsString = partialCustParamsString.concat(mappedGlobalParamString);
		const videoUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${incontentpreroll.unitId}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x360${fullCustParamsString}&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;
		// const videoUrl = 'https://pubads.g.doubleclick.net/gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&unviewed_position_start=1&cust_params=deployment%3Ddevsite%26sample_ct%3Dlinear&correlator=';

		try {
			playVimeoIMAAds(videoUrl, vimeoControl);
		} catch (err) {
			console.log('Uncaught Error while playing preroll', err);
			console.log('Attempting to mask error');
			await vimeoControl.prerollCallback();
		}
	}


