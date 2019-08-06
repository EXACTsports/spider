<?php

namespace App\Console\Commands;

use App\Actions\CreateExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
    public function handle()
    {
        $name = $this->ask('What is the name of the school you are using to develop this extractor?');
        $url = $this->ask('Paste the URL of the page you want to use as a test for your extractor here:');
        $is_profile = false;
        if ($this->confirm('Is this a profile page?')) {
            $is_profile = true;
            $name .= ' Profile';
        }

        $name = Str::studly($name);

        $extractor = new CreateExtractor($name, $url, $is_profile);
        $extractor->execute();

        if ($extractor->status == 'error') {
            $this->error($extractor->message);
        }
        else {
            $this->info($extractor->message);
        }
    }
}
