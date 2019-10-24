<?php

namespace Tests\Unit;

use DomDocument;
use Tests\TestCase;
use App\Actions\Extractors\IsacWymanside;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Do not edit or remove comment:
 * Extractor Based On: https://uclabruins.com/staff.aspx.
 */
class IsacWymansideTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(storage_path('test_pages/IsacWymanside.html'));
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
    }

    public function test_contacts_found()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        $extract = new IsacWymanside($dom, 'https://uclabruins.com/staff.aspx');
        $extract->execute();

        $this->assertInstanceOf('Illuminate\Support\Collection', $extract->contacts);
        $this->assertEquals(268, $extract->contacts->where('email', '<>', '')->count());
        $this->assertEquals(40, count($extract->meta['team_phones']));
    }
}
