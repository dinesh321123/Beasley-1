// Function for ajax get request_listicle
const request_listicle = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'GET',
		data,
		success: resolve,
		error: reject,
	});
});

// custom state : this controller contains your application logic
const mediaControllerListicle = wp.media.controller.State.extend({
	initialize: function () {
		// this model contains all the relevant data needed for the application
		this.props = new Backbone.Model({ custom_data_listicle: '' });
		this.props.on('change:custom_data_listicle', this.refresh, this);
	},

	// called each time the model changes
	refresh: function () {
		// update the toolbar
		this.frame.toolbar.get().refresh();
	},

	// called when the toolbar button is clicked
	customAction: function () {
		console.log(this.props.get('custom_data_listicle'));
	}

});

// custom toolbar : contains the buttons at the bottom
const mediaToolbarListicle = wp.media.view.Toolbar.extend({
	initialize: function () {
		_.defaults(this.options, {
			event: 'custom_listicle_event',
			close: false,
			items: {
				custom_listicle_event: {
					text: wp.media.view.l10n.customButtonListicle, // added via 'media_view_strings' filter,
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
		'click .media-button-custom_listicle_event': 'customclickevent',
	},

	customclickevent: function customclickevent() {
		const value = jQuery('#listicle_selected_id').val()
		const slug = jQuery('#listicle_selected_slug').val()
		
		jQuery('textarea#content').trigger('click');
		if( value ) {
			if( ! tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
				jQuery('textarea#content').val(jQuery('textarea#content').val() + '<br>[select-listicle listicle_id="'+value+'" syndication_name="'+slug+'" description="yes"]');
			} else {
				tinyMCE.execCommand('mceInsertContent', false, '<br>[select-listicle listicle_id="'+value+'" syndication_name="'+slug+'" description="yes"]<br>');
			}
			jQuery('.select-listicle-ul li').removeClass(' . $jqueryEventSelectedClass .');
			jQuery('.select-listicle-ul li').css('box-shadow', '0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)');
			jQuery('.media-button-custom_listicle_event').attr('disabled', 'disabled');
			jQuery('#listicle_selected_id').val(" ");
			jQuery('#listicle_selected_slug').val(" ");

			jQuery('.media-modal-close').trigger('click'); // Close the popup
		}

	},

	// called each time the model changes
	refresh: function () {
		// you can modify the toolbar behaviour in response to user actions here
		// disable the button if there is no custom data
		var custom_data_listicle = this.controller.state().props.get('custom_data_listicle');
		this.get('custom_listicle_event').model.set('disabled', !custom_data_listicle);

		// call the parent refresh
		wp.media.view.Toolbar.prototype.refresh.apply(this, arguments);
	},

	// triggered when the button is clicked
	customAction: function () {
		this.controller.state().customAction();
	}
});

// custom content : this view contains the main panel UI
const mediaViewListicle = wp.media.View.extend({
	className: 'listicle-selector',
	template: wp.template('listicle-selector'),
	
	events: {
		'click .s_btn_mediaimage': 'searchMediaImg',
		'click #media_loadmore': 'loadMoreMediaImg',
		'click .select-exist-listicle-li': 'selectexistinglisticle',
	},
	
	searchMediaImg: function searchMediaImg() {
		const $el = this.$el;
		const $preview = $el.find('.selectlisticle__preview');
		const $sSpinner = $el.find('#s_spinner');

		$sSpinner.addClass('is-active'); // load spinner

		request_listicle('get_listicle_cpt_data', {
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
		const $previewMediaImgUl = $el.find('.select-listicle-ul');
		const $pageNumber = $el.find('#page_number');
		const $loadmoreSpinner = $el.find('#loadmore_spinner');

		$mediaLoadmore.attr('disabled', 'disabled');
		$loadmoreSpinner.addClass('is-active'); // spinner load

		request_listicle('load_more_listicle_cpt_data', {
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
	selectexistinglisticle: function selectexistinglisticle() {
		const $el = this.$el;
		const self = this;
		const $listicleId = self.$el.find('.selected-listicle-thumbnail').attr('listicle-id');
		const $slugName = self.$el.find('.selected-listicle-thumbnail').attr('slug-name');
		
		if($listicleId) {
			self.$el.find('.selected-listicle-thumbnail').css('box-shadow', "0 0 0 0px #fff, 0 0 0 6px #0073aa")
			self.$el.find('#listicle_selected_id').val($listicleId);
			self.$el.find('#listicle_selected_slug').val($slugName);
			jQuery('.media-button-custom_listicle_event').removeAttr('disabled');
			jQuery('textarea#content').trigger('click');
		}
	},
});


// supersede the default MediaFrame.Post view
var oldMediaFrameListicle = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrameListicle.extend({

	initialize: function () {
		oldMediaFrameListicle.prototype.initialize.apply(this, arguments);

		this.states.add([
			new mediaControllerListicle({
				id: 'existinglisticle-action',
				menu: 'default', // menu event = menu:render:default
				content: 'existinglisticle',
				title: wp.media.view.l10n.customMenuTitleListicle, // added via 'media_view_strings' filter
				priority: 51,
				toolbar: 'main-existinglisticle-action', // toolbar event = toolbar:create:main-existinglisticle-action
				type: 'link'
			})
		]);

		this.on('content:render:existinglisticle', this.customContentListicle, this);
		this.on('toolbar:create:main-existinglisticle-action', this.createCustomToolbarListicle, this);
		this.on('toolbar:render:main-existinglisticle-action', this.renderCustomToolbarListicle, this);
	},

	createCustomToolbarListicle: function (toolbar) {
		toolbar.view = new mediaToolbarListicle({
			controller: this
		});
	},

	customContentListicle: function () {
		// this view has no router
		this.$el.addClass('hide-router');

		// custom content view
		var view = new mediaViewListicle({
			controller: this,
			model: this.state().props
		});

		this.content.set(view);
	},
});
