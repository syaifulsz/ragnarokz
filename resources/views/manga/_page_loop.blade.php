<ul class="rz-page-group js-rz-page-group"
    data-inf-manga-slug="{{ $pages[0]->manga->_slug() }}"
    data-inf-chapter-slug="{{ $pages[0]->chapter->_slug() }}"
    data-inf-chapter-url="{{ $pages[0]->chapter->_url() }}"
    data-inf-chapter-inf8="{{ $pages[0]->chapter->_inf8() }}"
    data-inf-prev-chapter-url="{{ $pagination['prev'] ? $pagination['prev']->_url() : null }}"
    data-inf-prev-chapter-order="{{ $pagination['prev'] ? $pagination['prev']->_slugOrder() : null }}"
    data-inf-next-chapter-url="{{ $pagination['next'] ? $pagination['next']->_url() : null }}"
    data-inf-next-chapter-order="{{ $pagination['next'] ? $pagination['next']->_slugOrder() : null }}"
    >
    @foreach ($pages as $page)
        @if ($page->_image())
            <li class="rz-page-group__item" id="{{ $page->_order() }}" data-page-id="{{ $page->_order() }}">
                <img src="{{ $page->_image() }}" alt="{{ $page->manga->_title() }}" />
            </li>
        @endif
    @endforeach
</ul>
