<?php

namespace App\Actions;

use Domains\Colleges\Models\Directory;
use Domains\Colleges\Models\DirectoryScrape;
use DOMDocument;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Spatie\QueueableAction\QueueableAction;

class CrawlDirectory
{
    protected $directory;
    protected $uuid;
    protected $pdf_path;
    protected $html_path;

    use QueueableAction;

    public function __construct(Directory $directory)
    {
        $this->directory = $directory;
        $this->uuid = Str::orderedUuid();
        $this->pdf_path = storage_path('pdf/' . (string)$this->uuid . '.pdf');
        $this->html_path = storage_path('html/' . (string)$this->uuid . '.html');
    }

    public function execute(): void
    {


        $html = $this->fetchPage();


        Browsershot::html($html)->savePdf($this->pdf_path);

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        FullyQualify::transform($dom, $this->directory->url);

        $html = $dom->saveHTML();

        $matches = [];
        preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}/i', $html, $matches);

        DirectoryScrape::create([
            'directory_id' => $this->directory->id,
            'uuid' => Str::orderedUuid(),
            'email_count' => sizeof(array_unique($matches[0]))
        ]);
    }

    public function setPdfPath($path): void
    {
        $this->pdf_path = $path;
    }

    public function setHtmlPath($path): void
    {
        $this->html_path = $path;
    }

    public function fetchPage()
    {
        return Browsershot::url($this->directory->$url)->bodyHtml();
    }

}

