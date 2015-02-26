#!/usr/bin/env bash
set -e

# must be run from the plugins/marketron_migration directory

# Configuration parameters
marketron_export=wmgk_data_export_02_23_15.zip
url=wmgk1.greatermedia.dev
site=www.wmgk.com
config_file=wmgk_blog_cfg.csv
mapping=wmgk_mapping.csv
tags=wmgk_tags.csv
limit=2

# extract and clean XML files from zips
wp marketron_migration migrate \
	--marketron_export=$marketron_export \
	--fresh=0

function wpcmd {
	wp gmedia-migration import $2  \
		--url=$url                 \
		--type=$1                  \
		--site=$site --force       \
		--allow-root               \
		--config_file=$config_file \
		--mapping_file=$mapping    \
		--tags=$tags               \
		--limit=$limit
}

# marketron apps (sequence is important)
wpcmd  blog       migration_cache/marketron_export/Blogs_formatted.xml
wpcmd  channel    migration_cache/marketron_export/Channels_formatted.xml
wpcmd  feed       migration_cache/marketron_export/Feeds_formatted.xml
wpcmd  venue      migration_cache/marketron_export/Venues_formatted.xml
wpcmd  event      migration_cache/marketron_export/EventCalendars_formatted.xml
wpcmd  concert    migration_cache/marketron_export/EventManager_formatted.xml
wpcmd  photoalbum migration_cache/marketron_export/PhotoAlbumsV2_formatted.xml
wpcmd  podcast    migration_cache/marketron_export/Podcasts_formatted.xml
wpcmd  showcase   migration_cache/marketron_export/Showcases_formatted.xml
wpcmd  video      migration_cache/marketron_export/VideoChannels_formatted.xml

# regenerate thumbnails
wp --url=$url --allow-root media regenerate --yes

# change ownership of media files
chown nginx:nginx ../../uploads/sites -R
