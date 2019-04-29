<?php
/**
 * Scout plugin for Craft CMS 3.x.
 *
 * Craft Scout provides a simple solution for adding full-text search to your entries. Scout will automatically keep your search indexes in sync with your entries.
 *
 * @link      https://rias.be
 *
 * @copyright Copyright (c) 2017 Rias
 */

namespace rias\scout\jobs;

use AlgoliaSearch\Index;
use Craft;
use craft\base\Element;
use craft\queue\BaseJob;
use rias\scout\Scout;

/**
 * @author    Rias
 *
 * @since     0.1.0e
 */
class DeIndexElement extends BaseJob
{
    // Properties
    // =========================================================================

    /* @var string */
    public $objectID;

    /* @var string */
    public $indexName;

    /* @var string */
    public $distinctId;

    /* @var Index */
    private $index;

    // Public Methods
    // =========================================================================

    /**
     * @throws \AlgoliaSearch\AlgoliaException
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @param craft\queue\QueueInterface $queue The queue the job belongs to
     *
     * @throws \AlgoliaSearch\AlgoliaException
     * @throws \Exception
     */
    public function execute($queue)
    {
        if (!$this->index) {
            $this->index = Scout::$plugin->scoutService->getClient()->initIndex($this->indexName);
        }

        if ($this->distinctId !== null) {
            $this->index->deleteBy([
                'filters' => 'distinctId:'.$this->distinctId,
            ]);
        } else {
            $this->index->deleteObject($this->objectID);
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    protected function defaultDescription(): string
    {
        return Craft::t('scout', sprintf('Removing element %s from index', $this->objectID));
    }
}
