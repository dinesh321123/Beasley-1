const { fvideo, wp } = window;

const request = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'GET',
		data,
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
	},

	onUrlChange() {
		const { $el } = this;
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

	addVideo() {
		const self = this;
		const url = self.$el.find('.video__url').val();
		const postId = wp.media.view.settings.post.id;

		if (self.loading) {
			return;
		}

		if (!url) {
			alert(fvideo.wrongUrl);
			return;
		}

		self.loading = true;
		request('fvideos_import_embed', { url, post_id: postId })
			.then((imageId) => {
				self.loading = false;

				const library = self.controller.content.mode('browse').get('library');

				library.options.selection.reset();
				library.collection.props.set({ ignore: +(new Date()) });

				library.collection.once('update', () => {
					const image = wp.media.attachment(imageId);
					if (image) {
						library.options.selection.add(image);
					}
				});
			})
			.catch(() => {
				self.loading = false;
				alert(fvideo.cannotEmbed);
			});
	},

});

export default mediaView;
