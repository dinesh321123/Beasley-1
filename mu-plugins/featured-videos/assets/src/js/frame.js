import FrameView from './view';

const frame = mediaFrame => mediaFrame.extend({

	bindHandlers(...params) {
		const self = this;

		mediaFrame.prototype.bindHandlers.apply(self, params);

		self.on('content:render:video', self.videoContent, self);
	},

	browseRouter(...params) {
		mediaFrame.prototype.browseRouter.apply(this, params);

		params[0].set({
			video: {
				text: 'Video',
				priority: 30,
			},
		});
	},

	videoContent() {
		const self = this;
		const view = new FrameView({
			controller: self,
		});

		self.$el.removeClass('hide-toolbar');
		self.content.set(view);
	},

});

export default frame;
