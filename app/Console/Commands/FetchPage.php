<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class FetchPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:page {url}';
    protected $url;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = $this->argument('url');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $html = Browsershot::url($this->url)->bodyHtml();
        try {
            Browsershot::html($html)->save(storage_path('pdf/'.$this->name.'.pdf'));
        } catch (CouldNotTakeBrowsershot $e) {
            $this->error($e->getMessage());
        }
    }
}
