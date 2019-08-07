<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DomDocument;
use App\Actions\Extractors\UniversityOfIllinois;

class UniversityOfIllinoisTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(storage_path('test_pages/UniversityOfIllinois.html'));
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
    }

    public function test_contacts_found()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        $extract = new UniversityOfIllinois($dom);
        $extract->execute();


        $this->assertInstanceOf('Illuminate\Support\Collection', $extract->contacts);
        $this->assertGreaterThan(300, $extract->contacts->count());

        // Do your own assertion(s) based on the actual contents of the file you are parsing.


    }
}
