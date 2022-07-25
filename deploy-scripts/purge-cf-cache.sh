#!/bin/bash
echo
echo
echo "Clearing Site Content Cache"
cf_tag=$1

while read -r line; do
  SITE=($(echo "$line" | tr '|' '\n'))
  ENDPOINT="https://api.cloudflare.com/client/v4/zones/${SITE[1]}/purge_cache"
  result=$(curl -s -X POST "$ENDPOINT" \
    -H "Content-Type: application/json" \
    -H "authorization: Bearer $CF_TOKEN" \
    --data '{"tags":["'$cf_tag'"]}')

  successvalue='"success": true'
  if [[ "$result" == *"$successvalue"* ]]; then
    echo "successfully cleared cache for ${SITE[0]}"
  else
    echo "failed to clear cache for ${SITE[0]}"
    if [[ -z $result ]]; then
      echo "Failed to return a value from cloudflare api."
    else
      echo "reply from cloudflare was ${result}"
    fi
  fi

  sleep 0.5

done\
  < \
<(grep -i '.*|.*|Enterprise Website' ./sites.txt)
