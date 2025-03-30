<?php

namespace modules\seed\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\User;
use craft\errors\ElementNotFoundException;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;
use yii\base\Exception;
use yii\console\ExitCode;
use yii\helpers\Console;
use function count;
use function str_replace;

/**
 * Faker controller
 */
class DefaultController extends Controller
{
    public $defaultAction = 'index';
    public const SECTION_HANDLE = 'article';
    public const TYPE_HANDLE = 'page';
    public const NUM_ENTRIES = 20;


    protected Generator $faker;

    public function beforeAction($action): bool
    {
        $this->faker = Factory::create();
        return parent::beforeAction($action);
    }


    public function actionIndex()
    {
        if (!$this->hasImages()) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->actionSetDefaultContent();
        $this->actionCreateArticles();
        $this->actionSetAlt();
        $this->actionCreateTransforms();
        return ExitCode::OK;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Throwable
     * @throws Exception
     */
    public function actionSetDefaultContent(): int
    {
        if (!$this->hasImages()) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->interactive && !$this->confirm("Set default content for single entries?", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Setting default content" . PHP_EOL);

        $entry = Entry::find()->section('home')->site('en')->one();
        $entry->title = 'Craft Boilerplate';
        $entry->featuredImage = [$this->getRandomImageId()];
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('home')->site('de')->one();
        $entry->title = 'Craft Boilerplate';
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('articleListing')->site('en')->one();
        $entry->title = 'Articles';
        $entry->featuredImage = [$this->getRandomImageId()];
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('articleListing')->site('de')->one();
        $entry->title = 'Artikel';
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('search')->site('en')->one();
        $entry->title = 'Search';
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('search')->site('de')->one();
        $entry->title = 'Suche';
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('siteSettings')->site('en')->one();
        $entry->siteName = 'Craft Boilerplate';
        $entry->defaultFeaturedImage = [$this->getRandomImageId()];
        $entry->primaryNavigation = [
            Entry::find()->section('articleListing')->one()->id,
            Entry::find()->section('search')->one()->id,
        ];
        $entry->copyright = 'The Demo Inc.';
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('siteSettings')->site('de')->one();
        $entry->siteName = 'Craft Boilerplate';
        $entry->copyright = 'The Demo Inc.';
        Craft::$app->getElements()->saveElement($entry);

        $this->stdout("Default content set" . PHP_EOL);
        return ExitCode::OK;
    }


    public function actionCreateArticles(int $num = self::NUM_ENTRIES): int
    {
        if (!$this->hasImages()) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $section = Craft::$app->entries->getSectionByHandle(self::SECTION_HANDLE);
        if (!$section) {
            $this->stderr("Invalid section") . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $type = Craft::$app->entries->getEntryTypeByHandle(self::TYPE_HANDLE);
        if (!$type) {
            $this->stderr("Invalid entry type") . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->interactive && !$this->confirm("Create {$num} entries of type '{$section->name}'? Make sure a number of images exist!", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Creating {$num} entries of type '{$section->name}'." . PHP_EOL);

        $user = User::find()->admin()->one();

        for ($i = 1; $i <= $num; ++$i) {
            $title = $this->faker->text(50);
            $this->stdout("[{$i}/{$num}] $title");

            $entry = new Entry();
            $entry->sectionId = $section->id;
            $entry->typeId = $type->id;
            $entry->authorId = $user->id;
            $entry->title = str_replace('.', '', $title);
            $entry->postDate = $this->faker->dateTimeInInterval('-2 days', '-3 months');
            $entry->setFieldValue('teaser', $this->faker->text(40));
            $entry->setFieldValue('featuredImage', [$this->getRandomImageId()]);
            $entry->setFieldValue('bodyContent', [
                'sortOrder' => ['new1', 'new2', 'new3', 'new4'],
                'blocks' => [
                    'new1' => [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->faker->text(500),
                        ],
                    ],
                    'new2' => [
                        'type' => 'heading',
                        'title' => $this->faker->text(50),
                        'fields' => [
                            'headingLevel' => 'h2',
                        ],
                    ],
                    'new3' => [
                        'type' => 'image',
                        'fields' => [
                            'image' => [$this->getRandomImageId()],
                            'caption' => $this->faker->text(50),
                        ],
                    ],
                    'new4' => [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->faker->text(500),
                        ],
                    ],
                ]
            ]);

            if (!Craft::$app->elements->saveElement($entry)) {
                $this->stderr("Error saving entry: " . print_r($entry->getErrors(), true));
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->stdout(PHP_EOL);
        }

        return ExitCode::OK;
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws ElementNotFoundException
     */
    public function actionSetAlt(): int
    {
        if (!$this->hasImages()) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->interactive && !$this->confirm("Set provisional alt text for images?", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $images = Asset::find()->kind('image')->all();
        foreach ($images as $image) {
            Console::output($image->title);
            $image->alt = $this->faker->text(50);
            Craft::$app->getElements()->saveElement($image);
        }

        return ExitCode::OK;
    }

    public function actionCreateTransforms(): int
    {

        if ($this->interactive && !$this->confirm("Retrieve all entries? This will create missing image transforms.", true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $client = Craft::createGuzzleClient();
        $entries = Entry::find()->section('*')->uri(':notempty:')->all();
        $total = count($entries);
        Console::startProgress(0, $total, 'Retrieving...');

        foreach ($entries as $index => $entry) {
            Console::updateProgress($index + 1, $total);

            try {
                $response = $client->get($entry->url);
            } catch (GuzzleException $e) {
                continue;
            }
        }

        Console::endProgress();

        return ExitCode::OK;
    }

    private function getRandomImageId()
    {
        return Asset::find()->kind('image')->width('> 1000')->orderBy('rand()')->one()->id;
    }

    private function hasImages()
    {
        $imageCount = Asset::find()->kind('image')->width('> 1000')->count();
        if (!$imageCount) {
            $this->stdout("No images found." . PHP_EOL);
            return false;
        }

        return true;
    }

}
