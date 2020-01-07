<?php

namespace App\Console\Commands;

use App\Models\UnitidLookup;
use Domains\Colleges\Models\College;
use Illuminate\Console\Command;

class PopulateCollegeLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unitid:lookups';

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
        $colleges = College::all();

        foreach ($colleges as $college) {
            if (is_numeric($college->unitid)) {
                UnitidLookup::create(['college_id' => $college->id, 'unitid' => (int)$college->unitid]);
            }
        }
    }
}
