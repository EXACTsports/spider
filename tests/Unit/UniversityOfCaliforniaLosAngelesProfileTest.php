<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DomDocument;
use App\Actions\Extractors\UniversityOfCaliforniaLosAngelesProfile;

class UniversityOfCaliforniaLosAngelesProfileTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(storage_path('test_pages/UniversityOfCaliforniaLosAngelesProfile.html'));
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
    }

    public function test_contacts_found()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        $extract = new UniversityOfCaliforniaLosAngelesProfile($dom);
        $extract->execute();

        $this->assertInstanceOf('App\Models\DirectoryContact', $extract->contact);
        $this->assertGreaterThan(10000, strlen($extract->contact->bio)); // Don't do exact number because of variations in results of PHP Tidy cleanup
        $this->assertEquals('/images/2018/6/20/COACH_Peters_1_1_of_1_.jpg', $extract->contact->image_url);
    }
}
