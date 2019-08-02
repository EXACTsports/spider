<?php

namespace Tests\Feature;

use App\Actions\ListSidearmDirectories;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\ParseSidearmDirectory;
use App\Actions\ParseSidearmProfile;

class SidearmParserTest extends TestCase
{
    // Snapshot of https://uclabruins.com/staff.aspx at storage/test_directories/sidearm_sports.aspx as of 8/1/2019

    protected $body;

    public function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function can_read_test_file()
    {
        $this->body = file_get_contents(storage_path('test_directories/sidearm_sports.aspx'));
        $this->assertStringContainsString('Sidearm', $this->body);
    }

    /** @test */
    public function can_parse_test_file()
    {
        $this->body = file_get_contents(storage_path('test_directories/sidearm_sports.aspx'));
        $parse = new ParseSidearmDirectory($this->body);

        $contacts = $parse->execute();

        $this->assertEquals($contacts->take(-24)->shift()['name'], 'Kendall Gustafson');
    }

    /** @test */
    public function can_read_test_profile_file()
    {
        $this->body = file_get_contents(storage_path('test_directories/sidearm_profile.aspx'));
        $this->assertStringContainsString('sidearm-staff-member', $this->body);
    }

    /**  @test */
    public function can_parse_test_profile_file()
    {
        $this->body = file_get_contents(storage_path('test_directories/sidearm_profile.aspx'));
        $parse = new ParseSidearmProfile($this->body);

        $contact = $parse->execute();

        $this->assertEquals($contact['image_url'], '/images/2018/7/9/grant_mccasland.jpeg?width=300');
    }

    /** @test */
    public function can_list_sidearm_diretories()
    {
        $list = new ListSidearmDirectories();
        $directories = $list->execute();

        $this->assertGreaterThan(1000, $directories->count());
    }


}
