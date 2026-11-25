@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="paginasi" data-testid="paginasi">
        @if ($paginator->onFirstPage())
            <span aria-hidden="true">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">‹</a>
        @endif

        @foreach ($paginator->elements() as $element)
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif
            @if (is_int($element))
                @if ($element === $paginator->currentPage())
                    <span class="aktif" aria-current="page">{{ $element }}</span>
                @else
                    <a href="{{ $paginator->url($element) }}">{{ $element }}</a>
                @endif
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">›</a>
        @else
            <span aria-hidden="true">›</span>
        @endif
    </nav>
@endif
