<?php

namespace modules\seed;

use Craft;
use yii\base\Module as BaseModule;

/**
 * MainModule module
 *
 * @method static SeedModule getInstance()
 */
class SeedModule extends BaseModule
{
    public function init(): void
    {
        Craft::setAlias('@modules/seed', __DIR__);

        // This module is only for console commands, so we can keep it simple
        $this->controllerNamespace = 'modules\\seed\\console\\controllers';
    }

}
