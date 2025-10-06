<?php

namespace modules\faux\console\controllers;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;

/**
 * Faux controller
 */
class CopyController extends Controller
{
    public $defaultAction = 'index';

    /**
     * faux/copy command
     *
     * Generated with this prompt for copilot/ChatGPT 5:
     *
     * implement  actionIndex
     *
     * - in directory modules/faux find a file starting with CustomFieldBehavior and delete it, if exists
     *
     * - in directory storage /runtime/compiled_classes find a file starting with CustomFieldBehavior and copy it to modules/_faux
     */
    public function actionIndex(): int
    {
        $this->stdout("Starting faux sync...\n");
        $exit = ExitCode::OK;
        try {
            $fauxDir = Craft::getAlias('@modules/faux');
            if (!is_dir($fauxDir)) {
                throw new \RuntimeException("Missing directory: $fauxDir");
            }

            $compiledDir =
                Craft::$app->getPath()->getRuntimePath() . DIRECTORY_SEPARATOR . 'compiled_classes';
            if (!is_dir($compiledDir)) {
                throw new \RuntimeException("Missing directory: $compiledDir");
            }

            // Delete existing CustomFieldBehavior* files in _faux
            foreach (
                glob($fauxDir . DIRECTORY_SEPARATOR . 'CustomFieldBehavior*') ?: []
                as $oldFile
            ) {
                if (is_file($oldFile) && @unlink($oldFile)) {
                    $this->stdout('Deleted: ' . basename($oldFile) . "\n");
                }
            }

            $sourceFiles = glob($compiledDir . DIRECTORY_SEPARATOR . 'CustomFieldBehavior*') ?: [];
            if (!$sourceFiles) {
                $this->stderr("No CustomFieldBehavior* file found in $compiledDir\n");
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $sourceFile = $sourceFiles[0];
            $destFile = $fauxDir . DIRECTORY_SEPARATOR . basename($sourceFile);
            if (!@copy($sourceFile, $destFile)) {
                throw new \RuntimeException("Copy failed: $sourceFile -> $destFile");
            }
            $this->stdout('Copied ' . basename($sourceFile) . " to faux module\n");
        } catch (\Throwable $e) {
            $this->stderr('Error: ' . $e->getMessage() . "\n");
            $exit = ExitCode::UNSPECIFIED_ERROR;
        }
        return $exit;
    }
}
