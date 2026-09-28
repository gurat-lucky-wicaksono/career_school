<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_landing_page_renders_all_sections_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="beranda"', false);
        $response->assertSee('id="platform"', false);
        $response->assertSee('id="sekolah"', false);
        $response->assertSee('id="fitur"', false);
        $response->assertSee('id="cara-kerja"', false);
        $response->assertSee('id="statistik"', false);
        $response->assertSee('id="faq"', false);
    }

    public function test_landing_page_direct_section_routes(): void
    {
        $sections = ['beranda', 'platform', 'sekolah', 'fitur-unggulan', 'cara-kerja', 'statistik', 'faq'];

        foreach ($sections as $section) {
            $response = $this->get('/' . $section);
            $response->assertStatus(200);
            $response->assertSee('id="' . ($section === 'fitur-unggulan' ? 'fitur' : $section) . '"', false);
        }
    }
}
