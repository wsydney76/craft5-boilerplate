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
use yii\base\InvalidRouteException;
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
    public const ARTICLE_SECTION_HANDLE = 'article';
    public const ARTICLE_TYPE_HANDLE = 'page';

    public const TOPIC_SECTION_HANDLE = 'topic';
    public const TOPIC_TYPE_HANDLE = 'topic';
    public const VOLUME = 'images';
    public const NUM_ARTICLES = 20;
    public const NUM_TOPICS = 5;
    public const SEED_IMAGES_COPYRIGHT = 'Pixabay';

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
        $this->actionCreateTopics();
        $this->actionCreateArticles();
        $this->actionSetAlt();
        $this->actionCreateTransforms();
        return ExitCode::OK;
    }

    /**
     * Sets default content for single entries in the home, articleListing, search, and siteSettings sections.
     *
     * @throws ElementNotFoundException
     * @throws Throwable
     * @throws Exception
     */
    public function actionSetDefaultContent(): int
    {
        if (!$this->hasImages()) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (
            $this->interactive &&
            !$this->confirm('Set default content for single entries?', true)
        ) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('Setting default content' . PHP_EOL);

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

        $entry = Entry::find()->section('topicListing')->site('en')->one();
        $entry->title = 'Topics';
        $entry->featuredImage = [$this->getRandomImageId()];
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('topicListing')->site('de')->one();
        $entry->title = 'Themen';
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
            Entry::find()->section('topicListing')->one()->id,
            Entry::find()->section('search')->one()->id,
        ];
        $entry->copyright = 'The Demo Inc.';
        Craft::$app->getElements()->saveElement($entry);

        $entry = Entry::find()->section('siteSettings')->site('de')->one();
        $entry->siteName = 'Craft Boilerplate';
        Craft::$app->getElements()->saveElement($entry);

        $this->stdout('Default content set' . PHP_EOL);
        return ExitCode::OK;
    }

    public function actionCreateTopics(int $num = self::NUM_TOPICS): int
    {
        if (!$this->hasImages($num)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $section = Craft::$app->entries->getSectionByHandle(self::TOPIC_SECTION_HANDLE);
        if (!$section) {
            $this->stderr('Invalid section') . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $type = Craft::$app->entries->getEntryTypeByHandle(self::TOPIC_TYPE_HANDLE);
        if (!$type) {
            $this->stderr('Invalid entry type') . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (
            $this->interactive &&
            !$this->confirm(
                "Create {$num} entries of type '{$section->name}'? Make sure a number of images exist!",
                true,
            )
        ) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Creating {$num} entries of type '{$section->name}'." . PHP_EOL);

        $user = User::find()->admin()->one();

        for ($i = 1; $i <= $num; ++$i) {
            $title = $this->faker->text(20);
            $this->stdout("[{$i}/{$num}] $title");

            $entry = new Entry();
            $entry->sectionId = $section->id;
            $entry->typeId = $type->id;
            $entry->authorId = $user->id;
            // Don't let a title end with a dot
            $entry->title = rtrim($title, '.');
            $entry->postDate = $this->faker->dateTimeInInterval('-2 days', '-3 months');
            $entry->setFieldValue('teaser', $this->faker->text(40));
            $entry->setFieldValue('featuredImage', [$this->getRandomImageId()]);

            if (!Craft::$app->elements->saveElement($entry)) {
                $this->stderr('Error saving entry: ' . print_r($entry->getErrors(), true));
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->stdout(PHP_EOL);
        }

        return ExitCode::OK;
    }

    /**
     * Creates a number of articles in the article section.
     *
     * @param int $num
     * @return int
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws Throwable
     */
    public function actionCreateArticles(int $num = self::NUM_ARTICLES): int
    {
        if (!$this->hasImages($num)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $section = Craft::$app->entries->getSectionByHandle(self::ARTICLE_SECTION_HANDLE);
        if (!$section) {
            $this->stderr('Invalid section') . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $type = Craft::$app->entries->getEntryTypeByHandle(self::ARTICLE_TYPE_HANDLE);
        if (!$type) {
            $this->stderr('Invalid entry type') . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (
            $this->interactive &&
            !$this->confirm(
                "Create {$num} entries of type '{$section->name}'? Make sure a number of images exist!",
                true,
            )
        ) {
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
            // Don't let a title end with a dot
            $entry->title = rtrim($title, '.');
            $entry->postDate = $this->faker->dateTimeInInterval('-2 days', '-3 months');
            $entry->setFieldValue('teaser', $this->faker->text(40));
            $entry->setFieldValue('featuredImage', [$this->getRandomImageId()]);
            $entry->setFieldValue('topics', [$this->getRandomTopicId()]);
            $entry->setFieldValue('bodyContent', [
                'sortOrder' => ['new1', 'new2', 'new3', 'new4', 'new5'],
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
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->faker->text(500),
                        ],
                    ],
                    'new4' => [
                        'type' => 'image',
                        'fields' => [
                            'image' => [$this->getRandomImageId()],
                            'caption' => $this->faker->text(50),
                            // Set a random image width
                            'blockWidth' => $this->faker->randomElement([
                                'default',
                                'slim',
                                'wide',
                                'max',
                                'full',
                            ]),
                        ],
                    ],
                    'new5' => [
                        'type' => 'text',
                        'fields' => [
                            'text' => $this->faker->text(500),
                        ],
                    ],
                ],
            ]);

            if (!Craft::$app->elements->saveElement($entry)) {
                $this->stderr('Error saving entry: ' . print_r($entry->getErrors(), true));
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->stdout(PHP_EOL);
        }

        return ExitCode::OK;
    }

    /**
     * Sets provisional alt text for images that do not have alt text set.
     *
     * @throws Throwable
     * @throws Exception
     * @throws ElementNotFoundException
     */
    public function actionSetAlt(): int
    {
        if (!$this->hasImages()) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->interactive && !$this->confirm('Set provisional alt text for images?', true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $images = Asset::find()->kind('image')->hasAlt(false)->all();
        foreach ($images as $image) {
            Console::output($image->title);
            $image->alt = $this->faker->text(50);
            Craft::$app->getElements()->saveElement($image);
        }

        return ExitCode::OK;
    }

    /**
     * Retrieves all entries to create missing image transforms.
     *
     * @return int
     */
    public function actionCreateTransforms(): int
    {
        if (
            $this->interactive &&
            !$this->confirm(
                'Retrieve all entries? This will create missing image transforms.',
                true,
            )
        ) {
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

    /**
     * Gets a random image ID from the asset index.
     * @return int|null
     */
    private function getRandomImageId(): ?int
    {
        return Asset::find()->kind('image')->width('> 1000')->orderBy('rand()')->one()?->id;
    }

    private function getRandomTopicId(): ?int
    {
        return Entry::find()->section('topic')->orderBy('rand()')->one()?->id;
    }

    /**
     * Checks if there are enough images indexed in the asset index.
     *
     * @param int $num
     * @return bool
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws Throwable
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    private function hasImages(int $num = self::NUM_ARTICLES): bool
    {
        $hasSeedImagesIndexed = Asset::find()->folderPath('seed/')->exists();
        if (!$hasSeedImagesIndexed) {
            Craft::$app->runAction('index-assets/one', [self::VOLUME]);
            // Add copyright to seed images
            $this->actionAddCopyrightToSeedImages();
        }

        $query = Asset::find()->kind('image')->width('> 1000');

        if ($query->count() < $num) {
            $this->stdout("Could not find $num images." . PHP_EOL);
            return false;
        }

        return true;
    }

    /**
     * Make sure all seed images have a copyright set.
     *
     * @return void
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws Throwable
     */
    public function actionAddCopyrightToSeedImages(): void
    {
        $this->stdout('Adding copyright to seed images...');
        $seedImages = Asset::find()->folderPath('seed/')->copyright(':empty:')->all();
        foreach ($seedImages as $seedImage) {
            $seedImage->copyright = self::SEED_IMAGES_COPYRIGHT;
            Craft::$app->getElements()->saveElement($seedImage);
        }
        $this->stdout('Done' . PHP_EOL);
    }
}
