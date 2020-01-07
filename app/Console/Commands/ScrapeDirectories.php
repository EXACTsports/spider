<?php

namespace App\Console\Commands;

use Artisan;
use Domains\Colleges\Models\Directory;
use Illuminate\Console\Command;

class ScrapeDirectories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $directories = Directory::all();

        foreach ($directories as $directory) {
            $this->info('Scraping ' . $directory->url);
            try {
                Artisan::call('fetch:page', ['id' => $directory->id]);
            }catch(\Exception $e) {
                $this->error($e->getMessage());
            }


            sleep(10);
        }
    }
}
