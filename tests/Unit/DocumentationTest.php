<?php

namespace BinaryTorch\LaRecipe\Tests\Unit;

use BinaryTorch\LaRecipe\Models\Documentation;
use BinaryTorch\LaRecipe\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class DocumentationTest extends TestCase
{
    protected $documentation;

    public function setUp()
    {
        parent::setUp();

        $this->documentation = $this->app->make(Documentation::class);
    }

    /** @test */
    public function it_can_parse_content()
    {
        $this->assertEquals('<p>hello</p>', $this->documentation->parse('hello'));

        $this->assertEquals('<h1>hello</h1>', $this->documentation->parse('#hello'));
    }

    /** @test */
    public function it_replace_placeholders_with_the_given_version_and_route()
    {
        $this->assertEquals(
            'the current version is 1.1 and the route is /docs',
            $this->documentation->replaceLinks('1.1', 'the current version is {{version}} and the route is /{{route}}')
        );
    }

    /** @test */
    public function it_caches_the_requested_documentation_and_index_for_a_given_period()
    {
        // set the docs path and landing
        Config::set('larecipe.docs.path', 'tests/views/docs');
        Config::set('larecipe.docs.landing', 'foo');
        Config::set('larecipe.versions.default', '1.0');
        Config::set('larecipe.cache.enabled', '1.0');

        $this->documentation->getIndex('1.0');
        $this->assertNotEmpty(cache('larecipe.docs.1.0.index'));

        $this->documentation->get('1.0', 'foo');
        $this->assertNotEmpty(cache('larecipe.docs.1.0.foo'));

        $this->documentation->get('1.0', 'bar');
        $this->assertNull(cache('larecipe.docs.1.0.bar'));
    }

    /** @test */
    public function it_can_compile_blade_content()
    {
        $bladeContent = '{{ csrf_field() }}';
        $this->assertEquals('<?php echo e(csrf_field()); ?>', $this->documentation->compileBlade($bladeContent));
    }

    /** @test */
    public function it_can_render_blade_content()
    {
        $content = "{{ count(['foo', 'bar']) }}";
        $this->assertEquals(2, $this->documentation->renderBlade($content));
    }
}
