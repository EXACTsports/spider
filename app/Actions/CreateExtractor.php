<?php

namespace App\Actions;

use Faker\Generator;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CreateExtractor
{
    protected $name;
    protected $url;
    protected $is_profile;

    public $message;
    public $status;

    public function __construct($name, $url, $is_profile)
    {
        $this->name = $name;
        $this->url = $url;
        $this->is_profile = $is_profile;
        $this->message = '';
        $this->status = 'pending';
        $this->results = collect([]);

        foreach (glob(__DIR__.'/Extractors/*.php') as $file) {
            $extractors[] = basename($file, '.php');
        }
        $this->extractors = $extractors;
    }

    public function execute()
    {
        $commands = ['git pull', 'git checkout -b '.$this->name, 'git push origin '.$this->name, 'git branch -a'];
        foreach ($commands as $command) {
            $process = new Process(
                $command
            );
            $process->run();

            $this->results->push($process->getOutput());

            // executes after the command finishes
            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        if (in_array($this->name, $this->extractors)) {
            $this->status = 'error';
            $this->message .= 'An extractor named '.$this->name.' already exists.';

            return;
        }

        $html = Browsershot::url($this->url)->bodyHtml();
        file_put_contents(storage_path('test_pages/'.$this->name.'.html'), $html);

        $stub = file_get_contents(__DIR__.'/Extractors/Stub');

        $stub = preg_replace('/STUB/', $this->name, $stub);
        file_put_contents(__DIR__.'/Extractors/'.$this->name.'.php', $stub);

        $stubtest = file_get_contents(base_path('tests/Unit/STUB'));
        $stubtest = preg_replace('/STUB/', $this->name, $stubtest);
        file_put_contents(base_path('tests/Unit/'.$this->name.'Test.php'), $stubtest);

        $this->message .= 'Extractor created at App/Actions/Extractors/'.$this->name.'.php and test created at tests/Unit/'.$this->name.'Test.php';
        $this->status = 'done';
    }

    public function generateName()
    {
        $faker = new Generator();
        $this->name = Str::studly($faker->firstNameMale . $faker->city . $faker->firstNameFemale);
    }
}
