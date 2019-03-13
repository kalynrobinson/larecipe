<?php

namespace BinaryTorch\LaRecipe\Tests\Feature;

use BinaryTorch\LaRecipe\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class ShowDocumentationTest extends TestCase
{
    /** @test */
    public function a_guest_will_be_redirected_to_default_version_and_landing_page_if_root_url_is_visited()
    {
        Config::set('larecipe.docs.path', 'tests/views/docs');
        Config::set('larecipe.docs.landing', 'foo');

        $this->get('/docs')->assertRedirect('/docs/1.0/foo')->assertStatus(302);
    }

    /** @test */
    public function a_guest_can_access_any_documentation_if_auth_is_not_enabled()
    {
        // set the docs path and landing
        Config::set('larecipe.docs.path', 'tests/views/docs');
        Config::set('larecipe.docs.landing', 'foo');

        // set auth to false
        Config::set('larecipe.settings.auth', false);

        // guest can view foo page
        $this->get('/docs/1.0')
            ->assertViewHasAll([
                'title',
                'index',
                'content',
                'currentVersion',
                'versions',
                'currentSection',
                'canonical',
            ])
            ->assertSee('<h1>Foo</h1>')
            ->assertSee('Get Started')
            ->assertSee('Section 1')
            ->assertStatus(200);
    }

    /** @test */
    public function a_guest_will_be_redirected_to_default_version_if_the_given_version_not_exists()
    {
        // set the docs path and landing
        Config::set('larecipe.docs.path', 'tests/views/docs');
        Config::set('larecipe.docs.landing', 'foo');
        Config::set('larecipe.versions.published', ['1.0']);

        // guest can view foo page
        $this->get('/docs/2.0/foo')
            ->assertRedirect('/docs/1.0/foo')
            ->assertStatus(301);
    }

    /** @test */
    public function a_guest_may_not_get_contents_of_not_exists_documentation()
    {
        // set the docs path and landing
        Config::set('larecipe.docs.path', 'tests/views/docs');
        Config::set('larecipe.docs.landing', 'foo');

        // guest can view foo page
        $this->get('/docs/1.0/bar')
            ->assertStatus(404);
    }

    /** @test */
    public function only_auth_user_can_visit_docs_if_auth_option_is_enabled()
    {
        Config::set('larecipe.settings.auth', true);

        $this->get('/docs/1.0')->assertRedirect('login');
    }

    /** @test */
    public function only_authorized_users_can_access_viewLarecipe_gate_is_defined()
    {
        Gate::define('viewLarecipe', function ($user, $documentation) {
            return false;
        });

        $this->get('/docs/1.0')->assertStatus(403);
    }
}
