
(function ($) {
    var $document = $(document);
    $document.ready(function () {
		const elements = document.querySelectorAll(".stnplayer");

		for (let stni = 0, len = elements.length; stni < len; stni++) {
			const element = elements[stni];
			const extraAttributes = getParamsStnEmbed(element) ? getParamsStnEmbed(element) : {};
			const placeholder = document.createElement('div');
			placeholder.setAttribute(
				'id',
				extraAttributes.id || `__cd-${(stni + 1)}`,
			);
			placeholder.classList.add('placeholder');
			placeholder.classList.add(`placeholder-stnplayer`);

			element.parentNode.replaceChild(placeholder, element);

			renderSTNVideoMobile( placeholder.getAttribute('id'), extraAttributes );
		}
		stnvideorender();
    });
})(jQuery);

function stnvideorender() {
	if (window.stnvideos) {
		if (window.stnvideos.prevent) {
			// do nothing
		} else if (window.stnvideos.override) {
			window.stnvideos.override.render();
		} else if (window.stnvideos.default) {
			window.stnvideos.default.render();
		}
	}
	delete window.stnvideos;
}

function getParamsStnEmbed(element) {
	const { dataset } = element;

	return {
		fk: dataset.fk,
		cid: dataset.cid,
		videokey: dataset.key,
		type: dataset.type,
	};
}

function renderSTNVideoMobile(placeholder, dataset) {
	let fk = dataset.fk ? dataset.fk : '';
	let cid = dataset.cid ? dataset.cid : '';
	let videokey = dataset.videokey ? dataset.videokey : '';
	let type = dataset.type ? dataset.type : '';

	if (!window.stnvideos) {
		window.stnvideos = {};
	}

	const container = document.getElementById(placeholder);
	if (!container) {
		return;
	}

	if (videokey.toLowerCase() === 'none') {
		window.stnvideos.prevent = true;
	} else if (videokey) {
		if (type === 'featured') {
			if (ee_is_whiz_stn_video()) {
				window.stnvideos.prevent = true;
				return;
			}
			const hideCaption = () => {
				const postThumbnailFeaturedCaption = document.querySelector(
					'.description .post-thumbnail-caption',
				);

				if (postThumbnailFeaturedCaption) {
					postThumbnailFeaturedCaption.style.display = 'none';
				}
			};

			// prettier-ignore
			const featuredDiv = document.getElementsByClassName('post-thumbnail')[0];
			window.stnvideos.override = {
				render: () => {
					const stndiv = document.createElement('div');
					stndiv.className = `s2nPlayer k-${fk} lazy-image`;
					stndiv.style.backgroundColor = 'transparent';
					stndiv.setAttribute('data-type', 'float');

					const stn_barker_script = document.createElement('script');
					stn_barker_script.setAttribute('type', 'text/javascript');
					stn_barker_script.setAttribute(
						'src',
						`//embed.sendtonews.com/player3/embedcode.js?SC=${videokey}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
					);
					stn_barker_script.setAttribute('data-type', 's2nScript');

					if (featuredDiv) {
						featuredDiv.innerHTML = '';
						featuredDiv.classList.add('stn-video-thumbnail', 'thumbnail-video-jacapp', 'stn-video-jacapp');
						featuredDiv.appendChild(stndiv);
						featuredDiv.appendChild(stn_barker_script);

						hideCaption();
					} else {
						// prettier-ignore
						const description = document.getElementsByClassName('description')[0];
						if (description) {
							const thumbnailDiv1 = document.createElement('div');
							thumbnailDiv1.className = `post-thumbnail featured-media stn-video-thumbnail thumbnail-video-jacapp stn-video-jacapp`;
							thumbnailDiv1.appendChild(stndiv);
							thumbnailDiv1.appendChild(stn_barker_script);

							const thumbnailDiv2 = document.createElement('div');
							thumbnailDiv2.className = `post-thumbnail-wrapper`;
							thumbnailDiv2.appendChild(thumbnailDiv1);

							description.insertBefore(thumbnailDiv2, description.firstChild);

							hideCaption();
						}
					}
				},
			};
		} else {
			window.stnvideos.override = {
				render: () => {
					const stndiv = document.createElement('div');
					stndiv.className = `s2nPlayer k-${fk}`;
					stndiv.setAttribute('data-type', 'float');

					const stn_barker_script = document.createElement('script');
					stn_barker_script.setAttribute('type', 'text/javascript');
					stn_barker_script.setAttribute(
						'src',
						`//embed.sendtonews.com/player3/embedcode.js?SC=${videokey}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
					);
					stn_barker_script.setAttribute('data-type', 's2nScript');

					container.appendChild(stndiv);
					container.appendChild(stn_barker_script);
				},
			};
		}
	} else {
		window.stnvideos.default = {
			render: () => {
				const stndiv = document.createElement('div');
				stndiv.className = `s2nPlayer k-${fk} s2nSmartPlayer`;
				stndiv.setAttribute('data-type', 'float');

				const stn_barker_script = document.createElement('script');
				stn_barker_script.setAttribute('type', 'text/javascript');
				stn_barker_script.setAttribute(
					'src',
					`//embed.sendtonews.com/player3/embedcode.js?fk=${fk}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
				);
				stn_barker_script.setAttribute('data-type', 's2nScript');

				container.appendChild(stndiv);
				container.appendChild(stn_barker_script);
			},
			type: 'default',
		};
	}
}

function ee_is_whiz_stn_video() {
	let $whiz_pos = null;

	if($whiz_pos === null ) {
		$whiz_pos = navigator.userAgent.toLowerCase().indexOf('whiz');
		if($whiz_pos == -1) {
			$whiz_pos = false;
		}

		const $_GET = new URLSearchParams(location.search);
		var url_pos = $_GET.has('whiz');
		if(url_pos) {
			$whiz_pos = 1;
		}
	}

	return false !== $whiz_pos;
}