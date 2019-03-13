<h1>{{ $reflector->getName() }}</h1>

{!! $docblock['class']->getSummary() !!}

{!! $docblock['class']->getDescription() !!}

{{-- Table of Contents --}}

@if (count($docblock['properties']) > 0)
    {{ count($docblock['properties'])  }}
    <h2>Fields</h2>
    @foreach ($docblock['properties'] as $prop => $doc)
        <h3><code>{!! $prop !!}</code></h3>
        {!! $doc->getSummary() !!}
        {!! $doc->getDescription() !!}
    @endforeach
@endif

<h2>Methods</h2>

@foreach ($docblock['methods'] as $method => $doc)
    <h3><code>{!! $method !!}</code></h3>
    {!! $doc->getSummary() !!}

    <blockquote>
        {!! $doc->getDescription() !!}
    </blockquote>
@endforeach