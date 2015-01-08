/* globals is_gigya_user_logged_in, gigya_profile_path, get_gigya_user_field */
(function($) {

	var ProfileMenuApp = function() {

	};

	ProfileMenuApp.prototype = {

		run: function() {
			var $container = $('.header__account--container');
			$container.append(this.getMenu());

			var $avatar = $('.header__account--btn');
			$avatar.attr('href', this.getAvatarLink());

			var thumbnailURL = this.getThumbnailURL();
			if (thumbnailURL) {
				var $img = $('<img />', { src: thumbnailURL });
				$avatar.html($img);
			}
		},

		getAvatarLink: function() {
			var endpoint = is_gigya_user_logged_in() ? 'account' : 'login';
			return gigya_profile_path(endpoint);
		},

		getThumbnailURL: function() {
			return get_gigya_user_field('thumbnailURL');
		},

		getMenu: function() {
			var menu  = this.getMenuLabels();
			var n     = menu.length;
			var $menu = $('<ul class="header__account--links sub-menu"></ul>');
			var $li, $a, item;

			for ( var i = 0; i < n; i++ ) {
				item = menu[i];
				$li = $('<li></li>');

				$a = $('<a></a>', { href: gigya_profile_path(item.endpoint) });
				$a.text(item.label);
				$li.append($a);

				$menu.append($li);
			}

			return $menu;
		},

		getMenuLabels: function() {
			var menu;

			if (is_gigya_user_logged_in()) {
				menu = [
					{ label: 'Edit Account' , endpoint: 'account' } ,
					{ label: 'Logout'       , endpoint: 'logout' }
				];
			} else {
				menu = [
					{ label: 'Login/Register', endpoint: 'login' }
				];
			}

			return menu;
		}

	};

	$(document).ready(function() {
		var app = new ProfileMenuApp();
		app.run();
	});

}(jQuery));
