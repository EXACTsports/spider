<?php

namespace App\Actions;

use Spatie\Crawler\Crawler;
use App\CrawlObservers\DefaultDirectory;
use App\Models\Directory;
use Spatie\QueueableAction\QueueableAction;

class CrawlDirectory
{
    protected $id;
    protected $delay;
    protected $max_depth;
    protected $max_pages;

    use QueueableAction;

    public function __construct($id, $delay = 0, $max_depth = 0, $max_pages = 0)
    {
        $this->id = $id;
        $this->delay = $delay;
        $this->max_depth = $max_depth;
        $this->max_pages = $max_pages;
    }
    public function execute()
    {
        $observer = new DefaultDirectory($this->id);

        Crawler::create()
            ->setCrawlObserver( $observer)
            ->setMaximumDepth($this->max_depth)
            ->executeJavaScript()
            ->ignoreRobots()
            ->setDelayBetweenRequests($this->delay)
            ->startCrawling(Directory::find($this->id)->url ?? config('env.test_site'));
    }
}
