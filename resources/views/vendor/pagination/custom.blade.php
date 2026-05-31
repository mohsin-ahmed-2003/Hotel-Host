@if ($paginator->hasPages())
    <nav class="custom-pagination" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-flex">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-item" rel="prev"
                    aria-label="@lang('pagination.previous')">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="pagination-numbers">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="pagination-item dots" aria-disabled="true">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-item active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-item" rel="next"
                    aria-label="@lang('pagination.next')">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif

<style>
    .custom-pagination {
        display: flex;
        justify-content: right;
        /* margin-top: 32px;
        margin-bottom: 32px; */
    }

    .pagination-flex {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--card-bg);
        padding: 8px 12px;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    .pagination-item {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 6px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        user-select: none;
        background: transparent;
        border: 1px solid transparent;
    }

    .pagination-item:hover:not(.disabled):not(.active):not(.dots) {
        background: var(--bg-2);
        color: var(--primary);
        border-color: var(--border);
        transform: translateY(-1px);
    }

    .pagination-item.active {
        background: var(--primary);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .pagination-item.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .pagination-item.dots {
        cursor: default;
        color: var(--text-muted);
    }

    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pagination-item i {
        font-size: 12px;
    }

    /* Dark mode override for cleaner visuals */
    body.dark-mode .pagination-item.active {
        background: var(--primary);
        color: #fff;
    }
</style>