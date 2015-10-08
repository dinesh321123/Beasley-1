# Greater Media Gigya

This plugin uses Composer for autoloading classes. The `vendor`
directory is `gitignored`, so you need to tell composer to fetch the
packages first.

### Installing Composer

If you are using VVV, you already have composer installed!

### Installing Packages

You need to run the following command after checking out the repo.

```bash
$ composer install
```

On subsequent `git pull`s if the `composer.json` or `composer.lock` file
has changed, you will need to also update composer again with,

```bash
$ composer update
```


