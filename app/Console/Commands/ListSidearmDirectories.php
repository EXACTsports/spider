<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\ListSidearmDirectories as ListAction;

class ListSidearmDirectories extends Command
{
    protected $signature = 'list:sidearm';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $list = new ListAction();
        $directories = $list->execute();

        foreach ($directories as $directory) {
            dd($directory);
            $this->info($directory['client_url']);
        }
    }
}
