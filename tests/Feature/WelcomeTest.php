<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeTest extends TestCase
{
    /** @test */
    public function can_say_hello()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Hello');
    }
}
