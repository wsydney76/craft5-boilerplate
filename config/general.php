<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see \craft\config\GeneralConfig
 */

use craft\config\GeneralConfig;
use craft\helpers\App;

$isDev = App::env('CRAFT_ENVIRONMENT') === 'dev';
$isProd = App::env('CRAFT_ENVIRONMENT') === 'production';

return GeneralConfig::create()

    // ---------- From craftcms/craft ----------

    // Set the default week start day for date pickers (0 = Sunday, 1 = Monday, etc.)
    ->defaultWeekStartDay(1)
    // Prevent generated URLs from including "index.php"
    ->omitScriptNameInUrls()
    // Preload Single entries as Twig variables
    ->preloadSingles()
    // Prevent user enumeration attacks
    ->preventUserEnumeration()

    // ---------- Environment specific settings ----------

    ->devMode($isDev)
    ->allowAdminChanges($isDev)
    ->disallowRobots(!$isProd)
    ->enableTemplateCaching($isProd)

    // ---------- Custom settings ----------

    // Do not let revisions pile up
    ->maxRevisions(10)
    // Remove non-ASCII characters from filenames of uploads
    ->convertFilenamesToAscii()
    // Use only ASCII characters for automatically generated slugs
    ->limitAutoSlugsToAscii()
    // Do not allow temporary asset URLs
    ->generateTransformsBeforePageLoad()
    // Do not serve transformed images with lower quality
    ->optimizeImageFilesize(false)
    // Store CP resources in a git ignored directory, which may be safely deleted
    ->resourceBasePath( App::env('CRAFT_WEB_ROOT') . '/dist/cpresources')
    ->resourceBaseUrl(App::env('PRIMARY_SITE_URL') . '/dist/cpresources')
    // Append a version hash to asset URLs
    ->revAssetUrls();
