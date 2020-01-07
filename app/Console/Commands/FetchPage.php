<?php

namespace App\Console\Commands;

use App\Actions\FullyQualify;
use App\Models\Scrape;
use Carbon\Carbon;
use Domains\Colleges\Models\Directory;
use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class FetchPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:page {id}';
    protected $tidy_config;
    private $file_name;
    private $status;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->file_name = Str::orderedUuid();
        $this->status = 1;
        $directory = Directory::find((int)$this->argument('id'));

        libxml_use_internal_errors(true);

        $html = Browsershot::url($directory->url)->bodyHtml();

        $html = tidy_parse_string($html, $this->tidy_config, 'utf8');

        $dom = new DOMDocument;

        $dom->loadHTML($html);

        FullyQualify::transform($dom, $directory->url);

        try {
            Browsershot::html($dom->saveHTML())->hideHeader()->hideFooter()->save(storage_path('app/scrapes/'.(string)$this->file_name.'.pdf'));
            Storage::disk('local')->put('scrapes/'.(string)$this->file_name.'.html', $dom->saveHtml());
        } catch (CouldNotTakeBrowsershot $e) {
            $this->error('Unable to scrape ' . $directory->url);
            $this->status = 0;
        }

        $meta = $directory->meta;

        $crawl = [
                'crawled_at' => Carbon::now()->toDateTimeString(),
                'file_name' => $this->file_name
            ];

        $meta['crawls'] = is_array($meta['crawls']) ? array_push($meta['crawls'], $crawl) : [$crawl];

        foreach ($crawl as $k => $v) {
            $meta[$k] = $v;
        }

        $directory->save();

        Scrape::create([
            'directory_id' => $directory->id,
            'college_id' => $directory->college_id,
            'contacts_count' => 0,
            'uuid' => $this->file_name,
            'extracted_at' => null,
            'extractor' => null,
            'ok' => $this->status,
        ]);



    }
}
