<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DomDocument;
use App\Actions\Extractors\JohannaPortVladimir;

/**
 * Do not edit or remove comment:
 * Extractor Based On: https://fightingillini.com/staff.aspx
 */
class JohannaPortVladimirTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(storage_path('test_pages/JohannaPortVladimir.html'));
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
    }

    public function test_contacts_found()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        $extract = new JohannaPortVladimir($dom, 'https://fightingillini.com/staff.aspx');
        $extract->execute();


        $this->assertInstanceOf('Illuminate\Support\Collection', $extract->contacts);
        $this->assertGreaterThan(300, $extract->contacts->count());


    }
}
