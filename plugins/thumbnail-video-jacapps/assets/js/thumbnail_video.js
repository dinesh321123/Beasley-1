
(function ($) {
    var $document = $(document);
    $document.ready(function () {
        let $videoThumbDiv = $(".youtube");
        let videothumbcount = $videoThumbDiv.length;

        // Check if class exist
        if (videothumbcount) {
            let vti = 0;

            $videoThumbDiv.each(async function (el) {
                var $currentShare = $(this);

                let dataHtml = $currentShare.data("html");
                let dataTitle = $currentShare.data("title");
                let dataThumbnail = $currentShare.data("thumbnail");

                const fragment = document.createElement('div');
                fragment.innerHTML = dataHtml;

                let html = '';
                let src = '';
                const iframe = fragment.querySelector('iframe');
                if (iframe) {
                    const parts = iframe.src.split('?');
                    src = `${parts[0]}?${parts[1] || ''}&rel=0&showinfo=0&autoplay=1`;
                    iframe.src = src;
                    html = iframe.outerHTML;
                }

				const isFallback = await tvj_isFallbackThumbnail(dataThumbnail);
				const data = {
                    src: src,
                    html: html,
                    title: dataTitle,
                    thumbnail: dataThumbnail,
					isFallback: isFallback
                };

                if ($.trim(data.title) != "" && $.trim(data.thumbnail) != "") {
                    renderVideoThumbnailMobile($currentShare, data, vti);
                    vti++;
                }
            })
        }
    });
})(jQuery);

function tvj_onThumbnailStartClick(event, ele) {
	event.preventDefault();
	$el = jQuery(ele);
	let $lazyVideo = $el.parent('.lazy-video');
	let html = ``;
	if($lazyVideo) {
		html = $lazyVideo.find('.thumbnail-video-frame-jacapp').html();
	}
	if(html) {
		$lazyVideo.html(html);
	}
}

async function tvj_isFallbackThumbnail(thumbnail) {
	thumbnail = thumbnail
		.replace('/vi/', '/vi_webp/')
		.replace('hqdefault.jpg', 'mqdefault.jpg')
		.replace('.jpg', '.webp');
	const img = await this.tvj_checkImg(thumbnail);

	if (img) {
		if (img.naturalWidth === 120) {
			return true;
		}
		return false;
	} else {
		return true;
	}
};

async function tvj_checkImg(url) {
	const img = new Image();
	return new Promise((resolve, reject) => {
		img.onload = function() {
			resolve(img);
		};
		img.onerror = function() {
			resolve();
		};
		img.src = url;
	});
};

function renderVideoThumbnailMobile($el, data, vti = 0) {
    let { src, html, title, thumbnail, isFallback } = data;

    let webp = false;
    if(thumbnail) {
        if (thumbnail.indexOf('i.ytimg.com') !== false) {
			let webpThumbType = 'image/jpg';
			let webpThumbSrc = thumbnail.replace('hqdefault.jpg', 'mqdefault.jpg');
			if (!isFallback) {
				webpThumbType = 'image/webp';
				webpThumbSrc = thumbnail
					.replace('/vi/', '/vi_webp/')
					.replace('hqdefault.jpg', 'mqdefault.jpg')
					.replace('.jpg', '.webp');
			}
			webp = `<source srcSet="${webpThumbSrc}" type=${webpThumbType} />`;

            thumbnail = thumbnail.replace('hqdefault.jpg', 'mqdefault.jpg');
        }
    }

    let replaceHtml = `
            <div class="lazy-video thumbnail-video-jacapp">
                <div class="thumbnail-video-frame-jacapp">
                    ${html}
                </div>
                <a class="thumbnail-video-start" href='${src}' aria-label='Play ${title}' onclick="tvj_onThumbnailStartClick(event, this);">
                    <picture>
                        ${webp}
                        <img src="${thumbnail}" alt="${title}" />
                    </picture>
                </a>

                <button class="thumbnail-video-start" aria-hidden="true" type="button" onclick="tvj_onThumbnailStartClick(event, this);">
                    <svg width="68" height="48" viewBox="0 0 68 48">
                        <path
                            class="shape"
                            d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"
                        />
                        <path class="icon" d="M 45,24 27,14 27,34" />
                    </svg>
                </button>
        </div>
    `;
    $el.html(replaceHtml);
}