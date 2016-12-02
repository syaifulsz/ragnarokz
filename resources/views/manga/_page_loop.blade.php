<ul class="rz-page-group">
    @foreach ($pages as $page)
        <li class="rz-page-group__item" id="{{ $page->_order() }}" data-page-id="{{ $page->_order() }}">
            <img src="{{ $page->_image() }}" alt="{{ $page->manga->_title() }}" />
        </li>
    @endforeach
</ul>
