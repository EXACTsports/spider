<?php

namespace App\Actions;

use App\Models\Directory;
use Spatie\Browsershot\Browsershot;
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
    }

    public function execute()
    {
        $directory = Directory::findOrFail($this->id);

        $shot = $directory->meta['shot_method'] ?? 'shot';

        $html = self::$shot($directory->url);

        return $html;
    }

    private static function shot($url)
    {
        $html = Browsershot::url($url)->bodyHtml();
        Browsershot::html($html)->savePdf(storage_path('pdf/'.md5($url).'.pdf'));

        return $html;
    }
}
