<?php

use craft\helpers\App;

$useDevServer = App::env('CRAFT_ENVIRONMENT') === 'dev' && App::env('VITE_USE_DEV_SERVER');

// Settings must match the ones in vite.config.js
return [
    'checkDevServer' => $useDevServer,
    'devServerInternal' => 'http://localhost:3000/',
    'devServerPublic' => Craft::getAlias('@weburl') . ':3000',
    'errorEntry' => 'resources/js/app.js',
    'manifestPath' => Craft::getAlias('@webroot') . '/dist/assets/.vite/manifest.json',
    'serverPublic' => Craft::getAlias('@weburl')  . '/dist/assets/',
    'useDevServer' => $useDevServer,
];
