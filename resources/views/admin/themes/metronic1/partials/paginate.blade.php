@if ($paginator->hasPages())
    <div class="kt-pagination pagination-{{@$class}} kt-pagination--sm kt-pagination--info">
        <ul class="kt-pagination__links">
            @if (!$paginator->onFirstPage())
                <li class="kt-pagination__link--first">
                    <a href="{{ $paginator->previousPageUrl() }}"><i
                                class="fa fa-angle-double-left kt-font-info"></i></a>
                </li>
            @endif
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="kt-pagination__link--active">
                                <a>{{ $page }}</a>
                            </li>
                        @elseif (($page == $paginator->currentPage() + 1 || $page == $paginator->currentPage() + 2) || $page == $paginator->lastPage())
                            <li><a class="kt-pagination__link--next" href="{{ $url }}">{{ $page }}</a></li>
                        @elseif ($page == $paginator->lastPage() - 1)
                            <li class="kt-pagination__link--next">
                                <a>...</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <li class="kt-pagination__link--last">
                    <a href="{{ $paginator->nextPageUrl() }}"><i class="fa fa-angle-double-right kt-font-info"></i></a>
                </li>
            @endif
        </ul>
    </div>
@endif

