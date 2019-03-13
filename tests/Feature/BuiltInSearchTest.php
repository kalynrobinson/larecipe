<?php

namespace BinaryTorch\LaRecipe\Tests\Feature;

use BinaryTorch\LaRecipe\Models\Documentation;
use BinaryTorch\LaRecipe\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class BuiltInSearchTest extends TestCase
{
    protected $documentation;

    public function setUp()
    {
        parent::setUp();

        $this->documentation = $this->app->make(Documentation::class);
    }

    /** @test */
    public function can_search_within_givin_version_for_h1_h2_h3()
    {
        Config::set('larecipe.docs.path', 'tests/views/docs');

        // activate built-in search..
        Config::set('larecipe.search.enabled', true);
        Config::set('larecipe.search.default', 'internal');

        $this->get('/docs/search-index/1.0')
            ->assertStatus(200)
            ->assertJsonStructure([
                [
                    'path',
                    'title',
                    'headings',
                ],
            ]);

        $this->assertTrue(true);
    }
}
