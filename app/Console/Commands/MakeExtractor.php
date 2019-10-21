<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Actions\CreateExtractor;

class MakeExtractor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extractor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a directory extractor';

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
    public function handle(): void
    {

        $url = $this->ask('Paste the URL of the page you want to use as a test for your extractor here:');



        $extractor = new CreateExtractor($url);
        $extractor->execute();

        if ($extractor->status == 'error') {
            $this->error($extractor->message);
        } else {
            $this->table(['Type Of File', 'Path To File'], $extractor->files);
            $this->info('You should now be on git branch ' . $extractor->name . '. Run `git status` to verify.');
        }
    }
}
