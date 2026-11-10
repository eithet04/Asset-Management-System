<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guest_sees_the_login_form_at_the_home_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('auth.login');
        $response->assertSeeText('Sign in to your account');
    }
}
