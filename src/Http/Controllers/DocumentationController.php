<?php

namespace BinaryTorch\LaRecipe\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use BinaryTorch\LaRecipe\DocumentationRepository;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

class DocumentationController extends Controller
{
    /**
     * @var DocumentationRepository
     */
    protected $documentationRepository;

    /**
     * DocumentationController constructor.
     */
    public function __construct(DocumentationRepository $documentationRepository)
    {
        $this->documentationRepository = $documentationRepository;

        if (config('larecipe.settings.auth')) {
            $this->middleware(['auth']);
        }
    }

    /**
     * Redirect the index page of docs to the default version.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        $redirectURL = config('larecipe.versions.default').'/'.config('larecipe.docs.landing');

        return redirect()->route('larecipe.show', $redirectURL);
    }

    /**
     * Show a documentation page.
     *
     * @param  string      $version
     * @param  string|null $page
     * @return Response
     */
    public function show($version, $page = null)
    {
        $documentation = $this->documentationRepository->get($version, $page);

        if (Gate::has('viewLarecipe')) {
            $this->authorize('viewLarecipe', $documentation);
        }

        if ($this->documentationRepository->isNotPublishedVersion($version)) {
            return redirect($documentation->defaultVersionUrl.'/'.$page, 301);
        }

        try {
            $docblock = $this->buildDocBlock($page);
            $reflector = new \ReflectionClass(config("larecipe.docblocks.$page"));
            Log::info("Good");
        } catch (\ReflectionException $e) {
            $docblock = $reflector = null;
            Log::info("Bad");
        }

        return response()->view('larecipe::docs', [
            'title'          => $documentation->title,
            'index'          => $documentation->index,
            'content'        => $documentation->content,
            'currentVersion' => $version,
            'versions'       => $documentation->publishedVersions,
            'currentSection' => $documentation->currentSection,
            'canonical'      => $documentation->canonical,
            'docblock'       => $docblock,
            'reflector'      => $reflector
        ], $documentation->statusCode);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    private function buildDocBlock($page)
    {
        $factory  = DocBlockFactory::createInstance();
        Log::info(config("larecipe.docblocks.$page"));
        $reflector = new \ReflectionClass(config("larecipe.docblocks.$page"));
        $classBlock = $factory->create($reflector->getDocComment() ?: "/**  */");

        $methodBlocks = [];
        foreach ($reflector->getMethods() as $method) {
            $methodBlocks[$method->getName()] = $factory->create($method->getDocComment() ?: "/**  */");
        }

        $propertyBlocks = [];
        foreach ($reflector->getProperties() as $prop) {
            $docblock = $prop->getDocComment();
            $propertyBlocks[$prop->getName()] = $factory->create($docblock ?: "/**  */");
        }

        return ['class' => $classBlock, 'methods' => $methodBlocks, 'properties' => $propertyBlocks];
    }
}
