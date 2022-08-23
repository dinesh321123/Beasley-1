window.amSelectionIndex = false;

jQuery(document).ready(function () {
	jQuery('#insert-media-button').click(function (e) {
		window.amSelectionIndex = true;
	});
});

// Function for ajax get request_am
const request_am = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'GET',
		data,
		success: resolve,
		error: reject,
	});
});

// custom state : this controller contains your application logic
const mediaControllerAffiliateMarketing = wp.media.controller.State.extend({
	initialize: function () {
		// this model contains all the relevant data needed for the application
		this.props = new Backbone.Model({ custom_data_am: '' });
		this.props.on('change:custom_data_am', this.refresh, this);
	},

	// called each time the model changes
	refresh: function () {
		// update the toolbar
		this.frame.toolbar.get().refresh();
	},

	// called when the toolbar button is clicked
	customAction: function () {
		console.log(this.props.get('custom_data_am'));
	}

});

// custom toolbar : contains the buttons at the bottom
const mediaToolbarAffiliateMarketing = wp.media.view.Toolbar.extend({
	initialize: function () {
		_.defaults(this.options, {
			event: 'custom_am_event',
			close: false,
			items: {
				custom_am_event: {
					text: wp.media.view.l10n.customButtonAffiliateMarketing, // added via 'media_view_strings' filter,
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
		'click .media-button-custom_am_event': 'customclickevent',
	},

	customclickevent: function customclickevent() {
		const value = jQuery('#am_selected_id').val()
		const slug = jQuery('#am_selected_slug').val()
		
		jQuery('textarea#content').trigger('click');
		if( value ) {
			if( ! tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
				jQuery('textarea#content').val(jQuery('textarea#content').val() + '<br>[select-am am_id="'+value+'" syndication_name="'+slug+'"]');
			} else {
				tinyMCE.execCommand('mceInsertContent', false, '<br>[select-am am_id="'+value+'" syndication_name="'+slug+'"]<br>');
			}
			jQuery('.select-am-ul li').removeClass(' . $jqueryEventSelectedClass .');
			jQuery('.select-am-ul li').css('box-shadow', '0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)');
			jQuery('.media-button-custom_am_event').attr('disabled', 'disabled');
			jQuery('#am_selected_id').val(" ");
			jQuery('#am_selected_slug').val(" ");

			jQuery('.media-modal-close').trigger('click'); // Close the popup
		}

	},

	// called each time the model changes
	refresh: function () {
		// you can modify the toolbar behaviour in response to user actions here
		// disable the button if there is no custom data
		var custom_data_am = this.controller.state().props.get('custom_data_am');
		this.get('custom_am_event').model.set('disabled', !custom_data_am);

		// call the parent refresh
		wp.media.view.Toolbar.prototype.refresh.apply(this, arguments);
	},

	// triggered when the button is clicked
	customAction: function () {
		this.controller.state().customAction();
	}
});


// custom content : this view contains the main panel UI
const mediaViewAffiliateMarketing = wp.media.View.extend({
	className: 'am-selector',
	template: wp.template( 'am-selector' ),
	
	events: {
		'click .s_btn_mediaimage': 'searchMediaImg',
		'click #media_loadmore': 'loadMoreMediaImg',
		'click .select-exist-am-li': 'selectexistingam',
	},
	
	searchMediaImg: function searchMediaImg() {
		const $el = this.$el;
		const $preview = $el.find('.selectam__preview');
		const $sSpinner = $el.find('#s_spinner');

		$sSpinner.addClass('is-active'); // load spinner

		request_am('get_am_cpt_data', {
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
		const $previewMediaImgUl = $el.find('.select-am-ul');
		const $pageNumber = $el.find('#page_number');
		const $loadmoreSpinner = $el.find('#loadmore_spinner');

		$mediaLoadmore.attr('disabled', 'disabled');
		$loadmoreSpinner.addClass('is-active'); // spinner load

		request_am('load_more_am_cpt_data', {
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
	selectexistingam: function selectexistingam() {
		const $el = this.$el;
		const self = this;
		const $amId = self.$el.find('.selected-am-thumbnail').attr('am-id');
		const $slugName = self.$el.find('.selected-am-thumbnail').attr('slug-name');
		
		if($amId) {
			self.$el.find('.selected-am-thumbnail').css('box-shadow', "0 0 0 0px #fff, 0 0 0 6px #0073aa")
			self.$el.find('#am_selected_id').val($amId);
			self.$el.find('#am_selected_slug').val($slugName);
			jQuery('.media-button-custom_am_event').removeAttr('disabled');
			jQuery('textarea#content').trigger('click');
		}
	},
});


// supersede the default MediaFrame.Post view
var oldMediaFrameAffiliateMarketing = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrameAffiliateMarketing.extend({

	initialize: function () {
		oldMediaFrameAffiliateMarketing.prototype.initialize.apply(this, arguments);

		if(window.amSelectionIndex !== false) {
			this.states.add([
				new mediaControllerAffiliateMarketing({
					id: 'existingam-action',
					menu: 'default', // menu event = menu:render:default
					content: 'existingam',
					title: wp.media.view.l10n.customMenuTitleAffiliateMarketing, // added via 'media_view_strings' filter
					priority: 52,
					toolbar: 'main-existingam-action', // toolbar event = toolbar:create:main-existingam-action
					type: 'link'
				})
			]);

			this.on('content:render:existingam', this.customContentAffiliateMarketing, this);
			this.on('toolbar:create:main-existingam-action', this.createCustomToolbarAffiliateMarketing, this);
			this.on('toolbar:render:main-existingam-action', this.renderCustomToolbarAffiliateMarketing, this);
			window.amSelectionIndex = false;
		}
	},

	createCustomToolbarAffiliateMarketing: function (toolbar) {
		toolbar.view = new mediaToolbarAffiliateMarketing({
			controller: this
		});
	},

	customContentAffiliateMarketing: function () {
		// this view has no router
		this.$el.addClass('hide-router');

		// custom content view
		var view = new mediaViewAffiliateMarketing({
			controller: this,
			model: this.state().props
		});

		this.content.set(view);
	},
});
