<?php

namespace App\Actions;

use Faker\Factory;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CreateExtractor
{
    protected $url;
    protected $is_profile;

    public $message;
    public $status;
    public $files;
    public $name;

    public function __construct($url)
    {
        $this->url = $url;
        $this->message = '';
        $this->status = 'pending';
        $this->results = collect([]);
        $this->files = [];

        foreach (glob(__DIR__.'/Extractors/*.php') as $file) {
            $extractors[] = basename($file, '.php');
        }
        $this->extractors = $extractors;
    }

    public function execute()
    {
        $this->generateName();

        while (in_array($this->name, $this->extractors)) {
            $this->generateName();
        }

        $commands = ['git checkout master', 'git fetch', 'git reset --hard origin/master', 'git checkout -b '.$this->name, 'git push origin '.$this->name, 'git branch -a'];
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

        $html = Browsershot::url($this->url)->bodyHtml();
        try {
            Browsershot::html($html)->save(storage_path('pdf/'.$this->name.'.pdf'));
        } catch (CouldNotTakeBrowsershot $e) {
            $this->status = 'error';
            $this->message = $e->getMessage();
        }
        file_put_contents(storage_path('test_pages/'.$this->name.'.html'), $html);

        $stub = file_get_contents(__DIR__.'/Extractors/Stub');

        $stub = preg_replace('/STUB/', $this->name, $stub);
        $stub = preg_replace('/URL/', $this->url, $stub);
        file_put_contents(__DIR__.'/Extractors/'.$this->name.'.php', $stub);

        $stubtest = file_get_contents(base_path('tests/Unit/STUB'));
        $stubtest = preg_replace('/STUB/', $this->name, $stubtest);
        $stubtest = preg_replace('/URL/', $this->url, $stubtest);
        file_put_contents(base_path('tests/Unit/'.$this->name.'Test.php'), $stubtest);
        $this->message = 'Success!';
        $this->status = 'done';
    }

    public function generateName()
    {
        $faker = Factory::create();
        $this->name = Str::studly($faker->firstName().$faker->city);

        $this->files = [
            ['Extractor Class', 'App/Actions/Extractors/'.$this->name.'.php'],
            ['Test To Write', 'tests/Unit/'.$this->name.'Test.php'],
            ['Source To Scrape', 'storage/test_pages/'.$this->name.'.html'],
        ];
    }
}
