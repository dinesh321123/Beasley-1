# Beasley WordPress

test

## Themes
The primary theme used throughout the Beasley properties is Experience Engine. You can can find docs specific to the theme can be found in the [Experience Enginge theme](themes/experience-engine/README.md)

## Must-Use Plugins - Sitewide Functionality & Integrations
The following is an overview of custom code that is used on each site. All of this code is located in `/mu-plugins/` and is automatically loaded on every site.

### Cache
`mu-plugins/classes/Bbgi/Cache.php`
Simple wrapper for WP_Cache

### DoubleClick for Publishers (DFP)
`mu-plugins/classes/Bbgi/Integration/Dfp.php`
 * Registers ACF metabox (checkbox) for marking content as sensitive
 * Updates Single Targeting to denote sensitive content if meta is present.
`themes/experience-engine/includes/dfp.php`

### Experience Engine
`mu-plugins/classes/Bbgi/Integration/ExperienceEngine.php`
Main API for interacting with Experience Engine.

### Firebase
`mu-plugins/classes/Bbgi/Integration/Firebase.php`
Firebase is used for user accounts. This module is responsible for registering network settings options for Firebase connection.

### Facebook
`mu-plugins/classes/Bbgi/Integration/Facebook.php`
Module responsible for adding Facebook pixel. Also registers Facebook settings for setting pixel ID.

### FeedPull
`/mu-plugins/classes/Bbgi/Integration/FeedPull.php`
FeedPull is a WordPress plugin used for syndicating posts from RSS feeds. This module is responsible for extending Feedpull plugin functionality through available hooks and filters.

### Google
`mu-plugins/classes/Bbgi/Integration/Google.php`
This module is responsible for Google Analytics (GA) and Tag Manager (GTM). Analytics code is assembled with post data and added to a placeholder div via data attributes. This allows React to handle submission to Google. GTM is embedded via iframe directly after the closing body tag.

### Image
`mu-plugins/classes/Bbgi/Image/`
This directory contains support for the following funcitonliaty:
* `Attributes.php` Adds 'attribution' field when adding an image via the media uploader
* `Layout.php` Adds 'Featured Image Layout' option in the post edit box.
* `ThumbnailColumn` Adds thumbnail preview column to the "All Posts" admin
  screen.

### Jacapps
`themes/experience-engine/includes/jacapps.php`
Creates a pared-down version of the site for use in Jacapps. Whether to show the normal or Jacapps template is determined by user agent (jacapps), however any page can be tested with the Jacapps template by simply appending `?jacapps` to the url.

### Media
`mu-plugins/classes/Bbgi/Media/Enclosure.php`
This module updates automatically updates post meta based on an uploaded podcast.
`mu-plugins/classes/Bbgi/Media/Video.php`
This module is responsible for adding embed support for multiple media providers.

### Push Notifications
`mu-plugins/classes/Bbgi/Integration/PushNotifications.php`
Module responsible for integrating EE Push Notifications.
* Adds user capabilities to send push notifications
* Adds post list action to send notification
* Creates notification menu page
* Adds ‘Send Notification’ metabox. This provides a button that sets up the correct url of the iframe to be embedded in the editor. It is the service in this embed that is responsible for sending the notification.

### Redirects
`mu-plugins/classes/Bbgi/Redirects.php`

## Second Street
`mu-plugins/classes/Bbgi/Integration/SecondStreet.php`
This module is responsible for creating the Second Street theme settings and generating the Second Street shortcode.

### Settings
`mu-plugins/classes/Bbgi/Settings.php`
This module is responsible for setting up the themes settings page.

`mu-plugins/functions/settings.php`
This file contains helpers used by the settings registration above.

### SEO
`mu-plugins/classes/Bbgi/Seo.php`

This module adds support for correct Twitter image sizes in the site's [Open
 Graph](https://ogp.me/) tags.

### Users
`mu-plugins/classes/Bbgi/Users.php`
Adds additional user functionality like last-login tracking and auto-expiration for idle accounts.

### Webhooks
`mu-plugins/classes/Bbgi/Users.php`
Support for Experience Engine webhooks on post create/update/delete.

## Plugins
The following in an overview of 10up-developed plugins that can be enabled on sites individually.

### greatermedia-admin-notifier
Provides interface for plugins to display messages to site administrators
### greatermedia-advertisers
This plugin is responsible for adding the 'Advertiser' post type
### greatermedia-announcements
This plugin is responsible for registering the 'Announcements' post type, as well as creating the 'Announcements' widget
### greatermedia-closures
Registers 'Closure' post type and associated metaboxes.
### greatermedia-content-auto-archive
Registers additional 'Archived' post status. In addition, sets up a cron job to auto archive posts of a specified age.
### greatermedia-content-cleanup
Provides functionality to remove auto-generated content from the site on either a cron job or via a WP-CLI command.
### greatermedia-content-syndication
Provides content syndication between subsites. See [Google Doc](https://docs.google.com/document/d/1nJUB9dngS7e8OxdkJ0l0804uG0g7a5ZEjvLlSO8JvM4/edit) for details.
### greatermedia-contests
Registers Contest post type and associated metaboxes. Provides auto contest invalidation upon contest completion.
### greatermedia-facebook-ia-extension
Customizes Facebook Instant Articles.
### greatermedia-galleries
Provides post types, metaboxes, admin interface updates, and functionality for Albums and Galleries
### greatermedia-keywords
Provides interface to associate posts with keywords.
### greatermedia-live-link
Functionality and support for creating live links.
### greatermedia-live-player
Legacy Live Player. Only used if current theme supports via `add_theme_support( 'legacy-live-player' )`
### greatermedia-live-stream
Functionality and support for posting live streams.
### greatermedia-podcasts
Functionality and support for Podcasts
### greatermedia-shows
Functionality and support for Shows
### greatermedia-simplifi
Functionality for adding Simplifi targeting and conversion pixels.
### greatermedia-timed-content
Functionality and support for expiring posts.

## Changelog

2020.12.08 Upgrade to WordPress core 4.9.16
