<?php

namespace App\Console\Commands;

use App\Models\KnownCoach;
use App\Models\UnitidLookup;
use Domains\Colleges\Models\College;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Statement;


class ImportValidator extends Command
{
    protected $file;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:validator {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a set of known coaches to validate crawl data.';

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
     */
    public function handle(): void
    {
        $csv = Reader::createFromPath(storage_path( $this->argument('file')), 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset

        $this->info('Purging known coaches table');
        $all = KnownCoach::all();
        foreach ($all as $coach) {
            $coach->delete();
        }
        $this->info('Importing known coaches');

        $stmt = (new Statement());

        $records = $stmt->process($csv);
        $bar = $this->output->createProgressBar(count($records));
        $bar->start();
        foreach ($records as $record) {
            if ($record['active'] === 'Y' && is_numeric($record['unitid'])) {

                $college = UnitidLookup::where('unitid', (int)$record['unitid'])->first();

                if ($college) {
                    $new = [
                        'college_id' => $college->college_id,
                        'sport' => Str::slug($record['sport']),
                        'gender' => 'm', // @todo - Fix this in the data for future versions
                        'name' => $record['name'] ?? '',
                        'email' => $record['email'] ?? '',
                        'title' => $record['title'] ?? ''
                    ];
                    KnownCoach::firstOrCreate(
                        ['name' => $new['name'], 'email' => $new['email']],
                        $new);
                }
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
