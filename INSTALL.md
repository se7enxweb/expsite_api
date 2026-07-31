# Installing expsite_api

## Requirements

- Exponential CMS (legacy) installation, PHP 8.1 or newer.
- No other extensions are required (the services use only kernel classes).

## Steps

1. Place the extension in `extension/expsite_api`.

2. Activate it in `settings/override/site.ini.append.php` (site-wide) or in a siteaccess `site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=expsite_api
   ```

   For a single siteaccess use `ActiveAccessExtensions[]` instead.

3. Regenerate the extension autoloads (the class map lives in `autoloads/expsite_api_autoload.php`):

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

4. Clear all caches:

   ```bash
   php bin/php/ezcache.php --clear-all --purge --allow-root-user
   ```

## Verifying

```php
$location = expSiteApi::location()->load( 2 );
// $location is an expSiteApiLocation or null
```

There is no configuration; the extension ships no INI settings.
