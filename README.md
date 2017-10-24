## Prod Configuration
The following constants are expected on prod:

* S3_UPLOADS_BUCKET
* S3_UPLOADS_KEY
* S3_UPLOADS_SECRET
* BEASLEY_PHOTON_DOMAIN

The following constants **should not** be set on prod:

* BEASLEY_IMAGE_DOMAIN

## Stage Configuration
The following constants are expected to be set on stage:

* S3_UPLOADS_BUCKET
* S3_UPLOADS_KEY
* S3_UPLOADS_SECRET
* BEASLEY_IMAGE_DOMAIN
* BEASLEY_PHOTON_DOMAIN

## Constant Reference

### BEASLEY_IMAGE_DOMAIN
**STAGE ONLY** If set, will replace the base domain of all images with the value of the constant. Include only the domain, with no http(s):// prefix.
This is intended to be used on stage, where we have a special domain that will first check local FS, then stage S3 bucket, then prod S3 bucket.

Example: `define( 'BEASLEY_IMAGE_DOMAIN', 'files.bbgistage.com' );`

### BEASLEY_PHOTON_DOMAIN
Must be set to rewrite images to photon. Should be the full domain (including http(s):// prefix) to the photon server.

Example: `define( 'BEASLEY_PHOTON_DOMAIN', 'https://photon.10uplabs.com' );`
