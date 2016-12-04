<ul class="rz-page-group js-rz-page-group"
    data-inf-manga-slug="{{ $pages[0]->manga->_slug() }}"
    data-inf-chapter-slug="{{ $pages[0]->chapter->_slug() }}"
    data-inf-prev-chapter-url="{{ $pagination['prev']->_url() }}"
    data-inf-prev-chapter-order="{{ $pagination['prev']->_slugOrder() }}"
    data-inf-next-chapter-url="{{ $pagination['next']->_url() }}"
    data-inf-next-chapter-order="{{ $pagination['next']->_slugOrder() }}"
    >
    @foreach ($pages as $page)
        <li class="rz-page-group__item" id="{{ $page->_order() }}" data-page-id="{{ $page->_order() }}">
            <img src="{{ $page->_image() }}" alt="{{ $page->manga->_title() }}" />
        </li>
    @endforeach
</ul>
