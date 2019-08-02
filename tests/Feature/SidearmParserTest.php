<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\ParseSidearmDirectory;

class SidearmParserTest extends TestCase
{
    // Snapshot of https://uclabruins.com/staff.aspx at storage/test_directories/sidearm_sports.aspx as of 8/1/2019

    protected $body;

    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(storage_path('test_directories/sidearm_sports.aspx'));
    }

    /** @test */
    public function can_read_test_file()
    {
        $this->assertStringContainsString('Sidearm', $this->body);
    }

    /** @test */
    public function can_parse_test_file()
    {
        $parse = new ParseSidearmDirectory($this->body);

        $contacts = $parse->execute();

        $this->assertEquals($contacts->take(-24)->shift()['name'], 'Kendall Gustafson');
    }
}
