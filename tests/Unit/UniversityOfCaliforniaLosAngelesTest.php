<?php

namespace Tests\Unit;

use DomDocument;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\Extractors\UniversityOfCaliforniaLosAngeles;

class UniversityOfCaliforniaLosAngelesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(storage_path('test_pages/UniversityOfCaliforniaLosAngeles.html'));
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
    }

    public function test_contacts_found()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        $extract = new UniversityOfCaliforniaLosAngeles($dom);
        $extract->execute();

        $this->assertInstanceOf('Illuminate\Support\Collection', $extract->contacts);
        $this->assertEquals(257, $extract->contacts->where('email', '<>', '')->count());
        $this->assertEquals(39, count($extract->meta['team_phones']));
    }
}
