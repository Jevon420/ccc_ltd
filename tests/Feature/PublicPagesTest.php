<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get(route('home'))->assertStatus(200);
    }

    public function test_about_page_loads(): void
    {
        $this->get(route('about'))->assertStatus(200);
    }

    public function test_services_page_loads(): void
    {
        $this->get(route('services'))->assertStatus(200);
    }

    public function test_projects_page_loads(): void
    {
        $this->get(route('projects'))->assertStatus(200);
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact'))->assertStatus(200);
    }

    public function test_home_page_contains_company_name(): void
    {
        $this->get(route('home'))
            ->assertSee('Constructive Cleaning');
    }

    public function test_home_page_contains_services(): void
    {
        $this->get(route('home'))
            ->assertSee('Development Advisory')
            ->assertSee('Land Maintenance')
            ->assertSee('Debris Cleaning');
    }
}
