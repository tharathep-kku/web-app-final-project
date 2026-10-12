@if ($paginator->hasPages())
    <div class="text-center">
        @if ($paginator->onFirstPage())
            <span class="btn btn-sm btn-secondary btn-pop disabled">&laquo; ก่อนหน้า</span>
        @else
            <a class="btn btn-sm btn-outline-primary btn-pop" href="{{ $paginator->previousPageUrl() }}">&laquo; ก่อนหน้า</a>
        @endif

        @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if ($page == $paginator->currentPage())
                <strong class="btn btn-sm btn-primary btn-pop">{{ $page }}</strong>
            @else
                <a class="btn btn-sm btn-outline-primary btn-pop" href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="btn btn-sm btn-outline-primary btn-pop" href="{{ $paginator->nextPageUrl() }}">ถัดไป &raquo;</a>
        @else
            <span class="btn btn-sm btn-secondary btn-pop disabled">ถัดไป &raquo;</span>
        @endif
    </div>
@endif
