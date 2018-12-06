#!/bin/bash
INPUT=data.csv
OLDIFS=$IFS
for site in $(wp site list --field=url)
	do
	echo " On site $site"

	# Import site settings.
	IFS=,
	[ ! -f $INPUT ] && { echo "$INPUT file not found"; exit 99; }
	while read -r key value
	do
		echo " Importing Key: '$key' with Value : '$value'"
		wp option update "$key" "$value" --url="$site"
	done < $INPUT
	IFS=$OLDIFS

	gmr_site_logo=$( wp option get gmr_site_logo --url="$site" )

	wp theme mod set custom_logo  "$gmr_site_logo" --url="$site"

	# Activate them
	wp theme activate experience-engine --url="$site"

	# Set menu menus to their locations.
	wp menu location assign ee-primary primary-nav --url="$site"
	wp menu location assign ee-about about-nav --url="$site"
	wp menu location assign ee-connect connect-nav --url="$site"

done