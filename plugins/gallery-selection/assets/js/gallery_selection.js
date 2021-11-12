// Function for ajax get request
const request = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'GET',
		data,
		success: resolve,
		error: reject,
	});
});

// custom state : this controller contains your application logic
wp.media.controller.Custom = wp.media.controller.State.extend({
	initialize: function () {
		// this model contains all the relevant data needed for the application
		this.props = new Backbone.Model({ custom_data: '' });
		this.props.on('change:custom_data', this.refresh, this);
	},

	// called each time the model changes
	refresh: function () {
		// update the toolbar
		this.frame.toolbar.get().refresh();
	},

	// called when the toolbar button is clicked
	customAction: function () {
		console.log(this.props.get('custom_data'));
	}

});

// custom toolbar : contains the buttons at the bottom
wp.media.view.Toolbar.Custom = wp.media.view.Toolbar.extend({
	initialize: function () {
		_.defaults(this.options, {
			event: 'custom_event',
			close: false,
			items: {
				custom_event: {
					text: wp.media.view.l10n.customButton, // added via 'media_view_strings' filter,
					style: 'primary',
					priority: 80,
					requires: false,
					click: this.customAction
				}
			}
		});
		wp.media.view.Toolbar.prototype.initialize.apply(this, arguments);
	},

	events: {
		'click .media-button-custom_event': 'customclickevent',
	},

	customclickevent: function customclickevent() {
		const value = jQuery('#gallery_selected_id').val()
		const slug = jQuery('#gallery_selected_slug').val()
		
		jQuery('textarea#content').trigger('click');
		if( value ) {
			if( ! tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
				jQuery('textarea#content').val(jQuery('textarea#content').val() + '<br>[select-gallery gallery_id="'+value+'" syndication_name="'+slug+'"]');
			} else {
				tinyMCE.execCommand('mceInsertContent', false, '<br>[select-gallery gallery_id="'+value+'" syndication_name="'+slug+'"]<br>');
			}
			jQuery('.select-gallery-ul li').removeClass(' . $jqueryEventSelectedClass .');
			jQuery('.select-gallery-ul li').css('box-shadow', '0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)');
			jQuery('.media-button-custom_event').attr('disabled', 'disabled');
			jQuery('#gallery_selected_id').val(" ");
			jQuery('#gallery_selected_slug').val(" ");

			jQuery('.media-modal-close').trigger('click'); // Close the popup
		}

	},

	// called each time the model changes
	refresh: function () {
		// you can modify the toolbar behaviour in response to user actions here
		// disable the button if there is no custom data
		var custom_data = this.controller.state().props.get('custom_data');
		this.get('custom_event').model.set('disabled', !custom_data);

		// call the parent refresh
		wp.media.view.Toolbar.prototype.refresh.apply(this, arguments);
	},

	// triggered when the button is clicked
	customAction: function () {
		this.controller.state().customAction();
	}
});

// custom content : this view contains the main panel UI
wp.media.view.Custom = wp.media.View.extend({
	className: 'gallery-selector',
	template: wp.template('gallery-selector'),
	
	events: {
		'click .s_btn_mediaimage': 'searchMediaImg',
		'click #media_loadmore': 'loadMoreMediaImg',
		'click .select-exist-gallery-li': 'selectexistinggallery',
	},
	
	searchMediaImg: function searchMediaImg() {
		const $el = this.$el;
		const $preview = $el.find('.selectgallery__preview');
		const $sSpinner = $el.find('#s_spinner');

		$sSpinner.addClass('is-active'); // load spinner

		request('get_gmr_gallery_data', {
			media: 'media_show',
			s_title: $el.find('#s_title').val(),
			s_tag: $el.find('#s_tag').val(),
			s_category: $el.find('#s_category').val()
		})
		.then((success) => {
			$preview.html(success.html);
			$sSpinner.removeClass('is-active');
		}).catch((error) => {
			$sSpinner.removeClass('is-active');
			console.log(error);
		});
	},
	loadMoreMediaImg: function loadMoreMediaImg() {
		const $el = this.$el;
		const $mediaLoadmore = $el.find('#media_loadmore');
		const $previewMediaImgUl = $el.find('.select-gallery-ul');
		const $pageNumber = $el.find('#page_number');
		const $loadmoreSpinner = $el.find('#loadmore_spinner');

		$mediaLoadmore.attr('disabled', 'disabled');
		$loadmoreSpinner.addClass('is-active'); // spinner load

		request('load_more_gmr_gallery_data', {
			media: 'media_show',
			s_title: $el.find('#s_title').val(),
			s_tag: $el.find('#s_tag').val(),
			s_category: $el.find('#s_category').val(),
			page_number: $el.find('#page_number').val()
		})
		.then((success) => {
			$previewMediaImgUl.append(success.media_image_list);
			$pageNumber.val(success.page_number);
			$loadmoreSpinner.removeClass('is-active');	// remove spinner load
			$mediaLoadmore.removeAttr('disabled');
			
			if (!success.media_image_list) {
				$mediaLoadmore.hide();
			}
		})
		.catch((error) => {
			console.log(error);
			$loadmoreSpinner.removeClass('is-active');	// remove spinner load
		});
	},
	selectexistinggallery: function selectexistinggallery() {
		const $el = this.$el;
		const self = this;
		const $galleryId = self.$el.find('.selected-gallery-thumbnail').attr('gallery-id');
		const $slugName = self.$el.find('.selected-gallery-thumbnail').attr('slug-name');
		
		if($galleryId) {
			self.$el.find('.selected-gallery-thumbnail').css('box-shadow', "0 0 0 0px #fff, 0 0 0 6px #0073aa")
			self.$el.find('#gallery_selected_id').val($galleryId);
			self.$el.find('#gallery_selected_slug').val($slugName);
			jQuery('.media-button-custom_event').removeAttr('disabled');
			jQuery('textarea#content').trigger('click');
		}
	},
});


// supersede the default MediaFrame.Post view
var oldMediaFrame = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrame.extend({

	initialize: function () {
		oldMediaFrame.prototype.initialize.apply(this, arguments);

		this.states.add([
			new wp.media.controller.Custom({
				id: 'my-action',
				menu: 'default', // menu event = menu:render:default
				content: 'custom',
				title: wp.media.view.l10n.customMenuTitle, // added via 'media_view_strings' filter
				priority: 50,
				toolbar: 'main-my-action', // toolbar event = toolbar:create:main-my-action
				type: 'link'
			})
		]);

		this.on('content:render:custom', this.customContent, this);
		this.on('toolbar:create:main-my-action', this.createCustomToolbar, this);
		this.on('toolbar:render:main-my-action', this.renderCustomToolbar, this);
	},

	createCustomToolbar: function (toolbar) {
		toolbar.view = new wp.media.view.Toolbar.Custom({
			controller: this
		});
	},

	customContent: function () {
		// this view has no router
		this.$el.addClass('hide-router');

		// custom content view
		var view = new wp.media.view.Custom({
			controller: this,
			model: this.state().props
		});

		this.content.set(view);
	},
});
