const { fvideo, FormData, wp } = window;

const request = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'GET',
		data,
		success: resolve,
		error: reject,
	});
});

const requestvideo = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'post',
		data,
		contentType: false,
		processData: false,
		success: resolve,
		error: reject,
	});
});


// @see wp.media.View.UploaderInline
const mediaView = wp.media.View.extend({

	tagName: 'div',
	className: 'video-embed-import',
	template: wp.template('video-embed-import'),

	events: {
		'keyup .video__url': 'onUrlChange',
		'click .video__submit': 'addVideo',
		'click .video__mediaimg': 'showMediaImg',
		'click #media_loadmore': 'loadMoreMediaImg',
		'click .s_btn_mediaimage': 'searchMediaImg',
		'click .img-attachment': 'getSelectedMediaImg',
		'click #upload_image': 'showUploadImage',
		'click #select_media_library': 'showMediaLibrary',
	},

	onUrlChange: function onUrlChange() {
		const $el = this.$el;

		const $preview = $el.find('.video__preview');
		const $submit = $el.find('.video__submit');

		request('fvideos_get_embed', { url: $el.find('.video__url').val() })
			.then((html) => {
				$preview.html(html);
				$submit.removeAttr('disabled');
			})
			.catch(() => {
				$preview.html('');
				$submit.attr('disabled', 'disabled');
			});
	},
	addVideo: function addVideo() {
		// const $el = this.$el;
		const self		= this;
		const url			= self.$el.find('.video__url').val();
		const postId		= wp.media.view.settings.post.id;
		const $selectedImageOption = self.$el.find("input[name='image_option']:checked").val();
		let fileName	= '';
		const fdata	= new FormData();

		if (!$selectedImageOption) {
			alert(fvideo.missingImage);
			return;
		}

		if (self.loading) {
			return;
		}

		if (!url) {
			alert(fvideo.wrongUrl);
			return;
		}

		fdata.append('action', 'fvideos_import_embed');
		fdata.append('image_option', $selectedImageOption);
		fdata.append('url', url);
		fdata.append('post_id', postId);

		if ($selectedImageOption === 'upload_image') {
			/* File upload code */
			const fileInputElement = document.getElementById('custom_featured_img');
			fileName = fileInputElement.files.length ? fileInputElement.files[0].name : '';

			if (fileName === '') {
				alert(fvideo.missingImage);
				return;
			}
			fdata.append('imagearr', fileInputElement.files[0], fileInputElement.files[0].name);
		}
		if ($selectedImageOption === 'select_media_library') {
			const $mediaImageId = self.$el.find('#media_image_id');

			if (!$mediaImageId) {
				alert(fvideo.missingMediaImage);
				return;
			}

			fdata.append('mediaImageId', $mediaImageId.val());
		}
		// fdata.append('key2', 'value2');
		/* console.log( 'Image name: ', fileInputElement.files[0].name );
		console.log('fileInputElement.files.length: ', fileInputElement.files.length );
		console.log( 'Form Data: ', fdata.entries() );
		console.log( 'Form key1 data: ', fdata.getAll('key1') ); */
		// Display the key/value pairs
		/* for (var pair of fdata.entries()) {
			console.log(pair[0]+ ', ' + pair[1]); console.log(' object, ', pair[1]);
		} */

		self.loading = true;
		// spinner load
		const $videoSubmitSpinner = self.$el.find('#video__submit_spinner');
		$videoSubmitSpinner.addClass('is-active');

		requestvideo('fvideos_import_embed', fdata).then((imageId) => {
			$videoSubmitSpinner.removeClass('is-active');	// remove spinner load
			self.loading = false;

			const library = self.controller.content.mode('browse').get('library');
			library.options.selection.reset();
			library.collection.props.set({ ignore: +new Date() });
			library.collection.once('update', () => {
				const image = wp.media.attachment(imageId);
				if (image) {
					library.options.selection.add(image);
				}
			});
		}).catch((error) => {
			console.log('Errors console : ', error);
			$videoSubmitSpinner.removeClass('is-active');	// remove spinner load
			self.loading = false;
			alert(fvideo.cannotEmbed);
		});
	},
	loadMoreMediaImg: function loadMoreMediaImg() {
		const $el = this.$el;
		const $mediaLoadmore = $el.find('#media_loadmore');
		const $previewMediaImgUl = $el.find('.mediaimg-ul');
		const $pagedMediaimage = $el.find('#paged_mediaimage');

		$mediaLoadmore.attr('disabled', 'disabled');
		// spinner load
		const $loadmoreSpinner = $el.find('#loadmore_spinner');
		$loadmoreSpinner.addClass('is-active');

		request('fvideos_load_more_media_image', { media: 'media_show', s_mediaimage: $el.find('#s_mediaimage').val(), paged_mediaimage: $el.find('#paged_mediaimage').val() }).then((success) => {
			$previewMediaImgUl.append(success.media_image_list);
			$pagedMediaimage.val(success.paged_mediaimage);
			$loadmoreSpinner.removeClass('is-active');	// remove spinner load
			$mediaLoadmore.removeAttr('disabled');
			// console.log( 'loadMoreMediaImg: ', success.imgs_array );
			if (!success.media_image_list) {
				$mediaLoadmore.hide();
			}
		}).catch((error) => {
			console.log(error);
			alert(fvideo.cannotEmbedImage);
			$loadmoreSpinner.removeClass('is-active');	// remove spinner load
		});
	},
	searchMediaImg: function searchMediaImg() {
		const $el = this.$el;
		const $preview = $el.find('.mediaimage__preview');
		// spinner load
		const $sSpinner = $el.find('#s_spinner');
		$sSpinner.addClass('is-active');

		request('fvideos_get_media_image', { media: 'media_show', s_mediaimage: $el.find('#s_mediaimage').val() }).then((success) => {
			$preview.html(success.html);
			// console.log( 'searchMediaImg: ', success.imgs_array );
		}).catch((error) => {
			console.log(error);
			alert(fvideo.cannotEmbedImage);
		});
	},
	showMediaImg: function showMediaImg() {
		const $el = this.$el;
		const $preview = $el.find('.mediaimage__preview');
		const $videoMediaimgButton = $el.find('.video__mediaimg');
		$videoMediaimgButton.attr('disabled', 'disabled');
		// spinner load when click on Open Media Library
		const $imagePreviewSpinner = $el.find('#image__preview_spinner');
		$imagePreviewSpinner.addClass('is-active');

		request('fvideos_get_media_image', { media: 'media_show' }).then((success) => {
			$imagePreviewSpinner.removeClass('is-active');	// remove spinner load
			$preview.html(success.html);
			// console.log( 'showMediaImg: ', success.imgs_array );
			$videoMediaimgButton.removeAttr('disabled');
		}).catch((error) => {
			console.log(error);
			alert(fvideo.cannotEmbedImage);
		});
	},
	getSelectedMediaImg: function getSelectedMediaImg() {
		const $el = this.$el;
		const $imagePreview = $el.find('.image__preview');
		const self = this;
		const $getImageAttrId = self.$el.find('.selected-media-img').attr('image-id');
		// spinner load when single thumbnail image
		const $imagePreviewSpinner = $el.find('#image__preview_spinner');
		$imagePreviewSpinner.addClass('is-active');
		$imagePreview.html('');

		if (!$getImageAttrId || $getImageAttrId === '') {
			alert(fvideo.missingImage);
			return;
		}
		request('get_selected_media_image', { imageAttrId: $getImageAttrId }).then((success) => {
			$imagePreview.html(success.single_image_div);
			self.$el.find('img').removeClass('selected-media-img');
			self.$el.find('#media_image_id').val($getImageAttrId);
			$imagePreviewSpinner.removeClass('is-active');	// remove spinner load
		}).catch((error) => {
			console.log(error);
			alert(fvideo.cannotEmbedImage);
		});
	},
	showUploadImage: function showUploadImage() {
		// alert( 'Select Upload image radio button' );
		const $el = this.$el;
		const $mediaImgLibrary = $el.find('.media__img__option');
		const $uploadImg = $el.find('.upload__img__option');
		const $mediaImgPreview = $el.find('.mediaimage__preview');
		const $ImgPreview = $el.find('.image__preview');

		$uploadImg.show();
		$mediaImgLibrary.hide();
		$mediaImgPreview.html('');
		$ImgPreview.html('');
	},
	showMediaLibrary: function showMediaLibrary() {
		// alert('Select Media library radio button');
		const $el = this.$el;
		const $mediaImgLibrary = $el.find('.media__img__option');
		const $uploadImg = $el.find('.upload__img__option');
		const $mediaImgPreview = $el.find('.mediaimage__preview');
		const $ImgPreview = $el.find('.image__preview');

		$mediaImgLibrary.show();
		$uploadImg.hide();
		$mediaImgPreview.html('');
		$ImgPreview.html('');
	},
});

export default mediaView;
