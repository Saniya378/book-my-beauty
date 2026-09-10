@if ($paginator->hasPages())
    <nav style="display: flex; justify-content: center; align-items: center; gap: 6px; flex-wrap: wrap; font-family: inherit;">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 8px 14px; border-radius: 6px; font-size: 13px; color: #ccc; border: 1px solid #eee; background: #fafafa;">&laquo; Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 6px; font-size: 13px; color: var(--primary); border: 1px solid var(--border); text-decoration: none;">&laquo; Previous</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="padding: 8px 12px; font-size: 13px; color: #999;">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding: 8px 13px; border-radius: 6px; font-size: 13px; font-weight: 700; background: var(--primary); color: white;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 8px 13px; border-radius: 6px; font-size: 13px; color: var(--primary); border: 1px solid var(--border); text-decoration: none;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 6px; font-size: 13px; color: var(--primary); border: 1px solid var(--border); text-decoration: none;">Next &raquo;</a>
        @else
            <span style="padding: 8px 14px; border-radius: 6px; font-size: 13px; color: #ccc; border: 1px solid #eee; background: #fafafa;">Next &raquo;</span>
        @endif

    </nav>
@endif