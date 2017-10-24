### Constants

##### BEASLEY_IMAGE_DOMAIN
**STAGE ONLY** If set, will replace the base domain of all images with the value of the constant. Include only the domain, with no http(s):// prefix.
This is intended to be used on stage, where we have a special domain that will first check local FS, then stage S3 bucket, then prod S3 bucket.

Example: `define( 'BEASLEY_IMAGE_DOMAIN', 'files.bbgistage.com' );`
