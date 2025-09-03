<?php

use craft\helpers\App;

$useDevServer = App::env('CRAFT_ENVIRONMENT') === 'dev' && App::env('VITE_USE_DEV_SERVER');

// Settings must match the ones in vite.config.js
return [


    /**
     * @var bool Should we check for the presence of the dev server by pinging $devServerInternal to make sure it's running?
     */
    'checkDevServer' => $useDevServer,


    /**
     * @var string The internal URL to the dev server, when accessed from the environment in which PHP is executing
     *              This can be the same as `$devServerPublic`, but may be different in containerized or VM setups.
     *              ONLY used if $checkDevServer = true
     */
    'devServerInternal' => 'http://localhost:5173/',

    /**
     * @var string The public URL to the dev server (what appears in `<script src="">` tags
     */
    'devServerPublic' => App::env('PRIMARY_SITE_URL') . ':5173',

    /**
     * @var string|array The JavaScript entry from the manifest.json to inject on Twig error pages
     *              This can be a string or an array of strings
     */
    'errorEntry' => 'resources/js/app.js',

    /**
     * @var string File system path (or URL) to the Vite-built manifest.json
     */
    'manifestPath' => App::env('CRAFT_WEB_ROOT') . '/dist/assets/.vite/manifest.json',

    /**
     * @var string The public URL to use when not using the dev server
     */
    'serverPublic' => App::env('PRIMARY_SITE_URL')  . '/dist/assets/',

    /**
     * @var bool Should the dev server be used?
     */
    'useDevServer' => $useDevServer,

    /**
     * @var bool Whether the modulepreload-polyfill shim should be included
     */
    'includeModulePreloadShim' => true,

    /**
     * @var bool Whether an onload handler should be added to <script> tags to fire a custom event when the script has loaded
     */
    'includeScriptOnloadHandler' => false,
];
