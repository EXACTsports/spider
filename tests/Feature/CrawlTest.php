<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Models\Directory;
use App\Actions\CrawlDirectory;
use Spatie\QueueableAction\ActionJob;

class CrawlTest extends TestCase
{
    protected $test_site;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->test_site = config('env.test_site');
    }

    /** @test */
    public function test_site_is_there()
    {
        $response = $this->get($this->test_site);

        $response->assertStatus(200);
        $response->assertSee('Laravel');
    }

    /** @test */
    public function can_create_test_directory()
    {
        $directory = factory(Directory::class)->make();

        $this->assertArrayHasKey('url', $directory->toArray());
        $this->assertEquals(config('env.test_site'), $directory->url);
    }

    /** @test */
    public function can_scrape_test_site()
    {
        Queue::fake();

        Queue::assertNothingPushed();
        $directory = factory(Directory::class)->make();
        $response = $this->get('/directory/'.$directory->id);
        $response->assertStatus(200);
        Queue::assertPushed(ActionJob::class);
    }


}
