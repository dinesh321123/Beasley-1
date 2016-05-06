(function($) {

	var config  = window.myemma_settings.data;
	var ajaxApi = new WpAjaxApi(config);

	var getTemplate = function(id) {
		var html = $('#' + id).html();
		return _.template(html);
	};

	var Screen = Backbone.Model.extend({
		defaults: {
			label: '',
			index: -1,
		}
	});

	var ApiKeysScreen = Screen.extend({
		defaults: {
			account_id: '',
			public_key: '',
			private_key: ''
		},

		authorize: function(opts) {
			var params = {
				emma_account_id: opts.account_id,
				emma_public_key: opts.public_key,
				emma_private_key: opts.private_key,
			};

			this.set(opts);

			ajaxApi.request('change_myemma_settings', params)
				.then($.proxy(this.didAuthorize, this))
				.fail($.proxy(this.didAuthorizeError, this));
		},

		didAuthorize: function(response) {
			if (response.success) {
				this.trigger('authorizeSuccess');
			} else {
				this.didAuthorizeError(response);
			}
		},

		didAuthorizeError: function(response) {
			this.trigger('authorizeError', response.data);
		}
	});

	var WebhooksScreen = Screen.extend({
		defaults: {
			auth_token: ''
		},

		initialize: function(attrs, opts) {
			Screen.prototype.initialize.call(this, attrs, opts);
			this.webhooks = [];
		},

		getWebhooks: function() {
			return this.webhooks;
		},

		loadWebhooks: function() {
			this.trigger('loadWebhooksStart');

			ajaxApi.request('list_myemma_webhooks', {})
				.then($.proxy(this.didLoadWebhooks, this))
				.fail($.proxy(this.didLoadWebhooksError, this));
		},

		didLoadWebhooks: function(response) {
			if (response.success) {
				this.setWebhooks(response.data);
				this.trigger('loadWebhooksSuccess');
			} else {
				this.didLoadWebhooksError(response);
			}
		},

		setWebhooks: function(webhooks) {
			if (webhooks.length === 0) {
				webhooks = [
					{ webhook_id: 'N/A', event: 'No webhooks found.' }
				];
			}

			this.webhooks = webhooks;
		},

		didLoadWebhooksError: function(response) {
			this.trigger('loadWebhooksError', response.data);
		},

		updateWebhooks: function(auth_token) {
			var params = {
				emma_webhook_auth_token: auth_token
			};

			this.trigger('updateStart');

			ajaxApi.request('update_myemma_webhooks', params)
				.then($.proxy(this.didUpdate, this))
				.fail($.proxy(this.didUpdateError, this));
		},

		didUpdate: function(response) {
			if (response.success) {
				this.trigger('loadWebhooksStart');
				this.setWebhooks(response.data);
				this.trigger('updateSuccess');
			} else {
				this.didUpdateError(response);
			}
		},

		didUpdateError: function(response) {
			this.trigger('updateError', response.data);
		}

	});

	var ScreenCollection = Backbone.Collection.extend({
		model: Screen,

		initialize: function(models, opts) {
			Backbone.Collection.prototype.initialize.call(this, models, opts);
			this._meta = {
				selectedIndex: 0
			};
		},

		meta: function(prop, value) {
			if (value === undefined) {
				return this._meta[prop];
			} else {
				var oldValue = this._meta[prop];
				this._meta[prop] = value;
				this.trigger('change:' + prop, value, oldValue);
			}
		}
	});

	var EmmaGroup = Backbone.Model.extend({
		defaults: {
			group_id: '',
			group_name: '',
			field_key: ''
		}
	});

	var EmmaGroupCollection = Backbone.Collection.extend({
		model: EmmaGroup,

		addGroup: function(opts) {
			var params = {
				emma_group_id: opts.group_id,
				emma_group_name: opts.group_name,
				gigya_field_key: opts.field_key,
				emma_group_description: opts.group_description,
				emma_group_active: opts.group_active,
				emma_group_opt_in: opts.opt_in_default
			};

			this.trigger('didAddStart');
			ajaxApi.request('add_myemma_group', params)
				.then($.proxy(this.didAdd, this))
				.fail($.proxy(this.didAddError, this));
		},

		removeGroup: function(group_id) {
			var params = {
				group_id: group_id
			};

			this.trigger('didRemoveStart');
			ajaxApi.request('remove_myemma_group', params)
				.then($.proxy(this.didRemove, this))
				.fail($.proxy(this.didRemoveError, this));
		},

		updateGroup: function(opts, group_id) {
			var params = {
				emma_group_id: opts.group_id,
				emma_group_name: opts.group_name,
				gigya_field_key: opts.field_key,
				group_to_update: group_id,
				emma_group_description: opts.group_description,
				emma_group_active: opts.group_active,
				emma_group_opt_in: opts.opt_in_default
			};

			this.trigger('didUpdateStart');
			ajaxApi.request('update_myemma_group', params)
				.then($.proxy(this.didUpdate, this))
				.fail($.proxy(this.didUpdateError, this));
		},

		didUpdate: function(response) {
			if (response.success) {
				var model = this.findWhere({ group_id: response.data.group_to_update });
				model.set({
					group_id: response.data.emma_group_id,
					group_name: response.data.emma_group_name,
					field_key: response.data.gigya_field_key,
					group_description: response.data.group_description,
					group_active: response.data.group_active
				});
				this.trigger('didUpdateSuccess');
			} else {
				this.didUpdateError(response);
			}
		},

		didUpdateError: function(response) {
			this.trigger('didUpdateError', response.data);
		},

		didRemove: function(response) {
			if (response.success) {
				var model = this.findWhere({ group_id: response.data });
				this.remove(model);
				this.trigger('didRemoveSuccess');
			} else {
				this.didRemoveError(response);
			}
		},

		didRemoveError: function(response) {
			this.trigger('didRemoveError', response.data);
		},

		didAdd: function(response) {
			if (response.success) {
				this.add([response.data]);
				this.trigger('didAddSuccess');
			} else {
				this.didAddError(response);
			}
		},

		didAddError: function(response) {
			this.trigger('didAddError', response.data);
		}
	});

	var GroupsScreen = Screen.extend({
		defaults: {
			label: '',
			index: -1,
			collection: null
		}
	});

	var ScreenNavView = Backbone.View.extend({
		template: getTemplate('nav'),
		events: {
			'click .nav a': 'didNavItemClick'
		},

		initialize: function(opts) {
			Backbone.View.prototype.initialize.call(this, opts);
			this.listenTo(this.collection, 'change:selectedIndex', this.render);
		},

		render: function() {
			var data = {
				screens: this.collection.toJSON(),
				selectedIndex: this.collection.meta('selectedIndex')
			};

			var html = this.template(data);
			this.$el.html(html);
		},

		didNavItemClick: function(event) {
			var index = $(event.target).data('index');
			index     = parseInt(index, 10);
			this.collection.meta('selectedIndex', index);
			return false;
		}
	});

	var ScreenView = Backbone.View.extend({
		currentStatusType: '',
		setStatus: function(type, message) {
			var $message = $('.status', this.$el);
			if (type === '') {
				$message.css('display', 'none');
			} else {
				$message.css('display', 'block');
			}

			$message.toggleClass(this.currentStatusType, false);
			$message.toggleClass('status', true);

			var $spinner = $('.spinner', this.$el);
			$spinner.css('display', type === 'progress' ? 'inline-block' : 'none');

			this.currentStatusType = type;

			if (type === 'progress') {
				type = 'updated progress';
			}

			$message.toggleClass(type, true);

			var $p = $('p', $message);
			$p.text(message);
		}
	});

	var GroupsScreenView = ScreenView.extend({
		template: getTemplate('groups'),
		events: {
			'click .create-group-button': 'didCreateClick',
			'click .add-group-button': 'didAddClick',
			'click .back-button': 'didBackClick',
			'click .remove-group-link': 'didRemoveClick',
			'click .edit-group-link': 'didEditClick',
		},

		initialize: function(opts) {
			ScreenView.prototype.initialize.call(this, opts);

			this.collection = this.model.get('collection');
			//this.listenTo(this.collection, 'change', this.render);
			this.listenTo(this.collection, 'didAddStart', this.didAddStart);
			this.listenTo(this.collection, 'didAddSuccess', this.didAddSuccess);
			this.listenTo(this.collection, 'didAddError', this.didAddError);

			this.listenTo(this.collection, 'didRemoveStart', this.didRemoveStart);
			this.listenTo(this.collection, 'didRemoveSuccess', this.didRemoveSuccess);
			this.listenTo(this.collection, 'didRemoveError', this.didRemoveError);

			this.listenTo(this.collection, 'didUpdateStart', this.didUpdateStart);
			this.listenTo(this.collection, 'didUpdateSuccess', this.didUpdateSuccess);
			this.listenTo(this.collection, 'didUpdateError', this.didUpdateError);
		},

		render: function() {
			this.renderGroups();
		},

		renderGroups: function() {
			var data = {
				groups: this.collection.toJSON(),
				view: this
			};

			if (data.groups.length === 0) {
				data.groups = [
					{ group_id: '', group_name: 'No groups found.', field_key: ''  }
				];
			}

			var html = this.template(data);

			$('.emma-groups', this.$el).css('display', 'block');

			$groups = $('.emma-groups table', this.$el);
			$groups.html(html);

			$newGroupContent = $('.new-group-content', this.$el);
			$newGroupContent.css('display', 'none');
		},

		renderEditor: function() {
			$title = $('.editor-title', this.$el);
			$submitButton = $('.create-group-button', this.$el);

			if (this.editMode) {
				$title.text('Edit MyEmma Group');
				$submitButton.val('Update');

				var group = this.collection.findWhere({group_id: this.groupToEdit});
				var group_active = group.get('group_active'),
					opt_in_default = group.get( 'opt_in_default' );

				if (group_active === undefined) {
					group_active = true;
				} else {
					group_active = !!group_active;
				}

				$('#emma_group_id').val(group.get('group_id'));
				$('#emma_group_name').val(group.get('group_name'));
				$('#gigya_field_key').val(group.get('field_key'));
				$('#emma_group_description').val(group.get('group_description'));
				$('#emma_group_active').attr('checked', group_active);
				$('#emma_group_opt_in').attr('checked', opt_in_default);
			} else {
				$title.text('New MyEmma Group');
				$submitButton.val('Create');

				this.clear();
			}

			$groups = $('.emma-groups', this.$el);
			$groups.css('display', 'none');

			$newGroupContent = $('.new-group-content', this.$el);
			$newGroupContent.css('display', 'block');
		},

		toEmmaGroupURL: function(groupID) {
			return 'https://app.e2ma.net/app2/audience/list/active/' + groupID + '/';
		},

		didAddClick: function(event) {
			this.editMode = false;
			this.renderEditor();
			this.setStatus('');
			return false;
		},

		didCreateClick: function(event) {
			var params = {
				group_id: $('#emma_group_id').val(),
				group_name: $('#emma_group_name').val(),
				field_key: $('#gigya_field_key').val(),
				group_description: $('#emma_group_description').val(),
				group_active: $('#emma_group_active').is(':checked'),
				opt_in_default: $('#emma_group_opt_in').is(':checked')
			};

			if (this.editMode) {
				this.collection.updateGroup(params, this.groupToEdit);
			} else {
				this.collection.addGroup(params);
			}
			return false;
		},

		didBackClick: function(event) {
			this.renderGroups();
			return false;
		},

		didAddStart: function() {
			this.setStatus('progress', 'Adding Group ...');
		},

		didAddSuccess: function() {
			this.setStatus('updated', 'Group added successfully.');
			this.clear();
		},

		didAddError: function(message) {
			this.setStatus('error', message);
		},

		didUpdateStart: function() {
			this.setStatus('progress', 'Updating Group ...');
		},

		didUpdateSuccess: function() {
			this.setStatus('updated', 'Group updated successfully.');
		},

		didUpdateError: function(message) {
			this.setStatus('error', message);
		},

		clear: function() {
			$('#emma_group_id').val('');
			$('#emma_group_name').val('');
			$('#gigya_field_key').val('');
			$('#emma_group_description').val('');
			$('#emma_group_active').attr('checked', false);
			$('#emma_group_opt_in').attr('checked', false);
		},

		didRemoveClick: function(event) {
			var group_id  = $(event.currentTarget).data('group');
			var confirmed = confirm('Confirm: Delete MyEmma Group - ' + group_id + '?');

			if (confirmed) {
				this.collection.removeGroup(group_id);
			}

			return false;
		},

		didRemoveStart: function() {
			this.setStatus('progress', 'Removing Group ...');
		},

		didRemoveSuccess: function() {
			this.setStatus('updated', 'Group removed successfully');
			this.renderGroups();
		},

		didRemoveError: function(message) {
			this.setStatus('error', message);
		},

		didEditClick: function(event) {
			this.setStatus('');

			var group_id     = $(event.currentTarget).data('group');
			this.editMode    = true;
			this.groupToEdit = group_id.toString();

			this.renderEditor();
			return false;
		}
	});

	var WebhooksScreenView = ScreenView.extend({
		template: getTemplate('webhooks'),
		events: {
			'click .update-webhooks-button': 'didUpdateClick'
		},

		initialize: function(opts) {
			ScreenView.prototype.initialize.call(this, opts);
			this.listenTo(this.model, 'loadWebhooksStart', this.didLoadWebhooksStart);
			this.listenTo(this.model, 'loadWebhooksSuccess', this.didLoadWebhooksSuccess);
			this.listenTo(this.model, 'loadWebhooksError', this.didLoadWebhooksError);

			this.listenTo(this.model, 'updateStart', this.didUpdateStart);
			this.listenTo(this.model, 'updateSuccess', this.didUpdateSuccess);
			this.listenTo(this.model, 'updateError', this.didUpdateError);
		},

		didLoadWebhooksStart: function() {
			this.setStatus('progress', 'Loading Webhooks ...');
			this.didLoadWebhooks = false;
		},

		didLoadWebhooksError: function(message) {
			this.setStatus('error', message);
			this.didLoadWebhooks = false;
		},

		didLoadWebhooksSuccess: function() {
			this.setStatus('');
			this.renderTable();
			this.didLoadWebhooks = true;
		},

		didUpdateClick: function(event) {
			var auth_token = $('#auth_token').val();
			this.model.updateWebhooks(auth_token);

			return false;
		},

		didUpdateStart: function() {
			this.setStatus('progress', 'Updating Webhooks ...');
		},

		didUpdateSuccess: function() {
			this.setStatus('updated', 'Webhooks updated.');
		},

		didUpdateError: function(message) {
			this.setStatus('error', message);
		},

		render: function() {
			$('#auth_token', this.$el).val(this.model.get('auth_token'));

			if (!this.didLoadWebhooks) {
				this.model.loadWebhooks();
			}
		},

		renderTable: function() {
			var data = {
				webhooks: this.model.getWebhooks()
			};
			var html = this.template(data);
			var $activeWebhooks = $('.active-webhooks', this.$el);
			$activeWebhooks.html(html);
		}
	});

	var ApiKeysScreenView = ScreenView.extend({
		events: {
			'click .authorize-button': 'didAuthorizeClick'
		},

		initialize: function(opts) {
			ScreenView.prototype.initialize.call(this, opts);

			this.listenTo(this.model, 'authorizeSuccess', this.didAuthorize);
			this.listenTo(this.model, 'authorizeError', this.didAuthorizeError);
		},

		render: function() {
			$('#account_id', this.$el).val(this.model.get('account_id'));
			$('#public_key', this.$el).val(this.model.get('public_key'));
			$('#private_key', this.$el).val(this.model.get('private_key'));
		},

		didAuthorizeClick: function(event) {
			var params = {
				account_id: $('#account_id', this.$el).val(),
				public_key: $('#public_key', this.$el).val(),
				private_key: $('#private_key', this.$el).val()
			};

			this.setStatus('progress', 'Saving Settings ...');
			this.model.authorize(params);

			return false;
		},

		didAuthorize: function() {
			this.setStatus('updated', 'Settings Saved.');
		},

		didAuthorizeError: function(message) {
			this.setStatus('error', message);
		}
	});

	var ScreenCollectionView = Backbone.View.extend({
		initialize: function(opts) {
			Backbone.View.prototype.initialize.call(this, opts);
			this.listenTo(this.collection, 'change:selectedIndex', this.render);
		},

		screenMeta: [
			{ index: 0 , screen: GroupsScreenView   , view: null } ,
			{ index: 1 , screen: WebhooksScreenView , view: null } ,
			{ index: 2 , screen: ApiKeysScreenView  , view: null } ,
		],

		render: function() {
			var selectedIndex = this.collection.meta('selectedIndex');
			var screens       = this.collection.toJSON();
			var self          = this;

			var $navContent = $('.emma-nav-content');
			$navContent.css('display', 'block');

			_.each(screens, function(screen, index) {
				var $screen = $('.emma-nav-content li[data-index=' + index + ']');
				var display = index === selectedIndex ? 'block' : 'none';
				var meta    = self.screenMeta[index];
				var view    = meta.view;

				if (view === null) {
					var model = self.collection.at(index);
					view = new meta.screen({ model: model, el: $screen });
					meta.view = view;
				}

				$screen.css('display', display);
				view.render();
			});
		},


	});

	var App = function() {
	};

	App.prototype = {

		run: function() {
			var emmaGroups= new EmmaGroupCollection(
				config.emma_groups
			);

			var screenCollection = new ScreenCollection([
				new GroupsScreen({
					label: 'Groups',
					index: 0,
					collection: emmaGroups
				}),
				new WebhooksScreen({
					label: 'Webhooks',
					index: 1,
					auth_token: config.emma_webhook_auth_token
				}),
				new ApiKeysScreen({
					label: 'API Keys',
					index: 2,
					account_id: config.emma_account_id,
					public_key: config.emma_public_key,
					private_key: config.emma_private_key
				}) ,
			]);

			var screenNavView = new ScreenNavView({
				collection:screenCollection,
				el: $('.emma-settings-nav')
			});

			var screenCollectionView = new ScreenCollectionView({
				collection:screenCollection,
				el: $('.emma-nav-content')
			});

			screenNavView.render();
			screenCollectionView.render();
		}

	};

	$(document).on('ready', function() {
		var app = new App();
		app.run();
	});

}(jQuery));
