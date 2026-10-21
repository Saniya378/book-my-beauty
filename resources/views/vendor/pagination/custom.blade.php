@if ($paginator->hasPages())
    <nav class="pagination">
        @if ($paginator->onFirstPage())
            <span class="pagination__item is-disabled">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination__item" rel="prev">Previous</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination__dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination__item is-current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination__item">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination__item" rel="next">Next</a>
        @else
            <span class="pagination__item is-disabled">Next</span>
        @endif
    </nav>
@endif