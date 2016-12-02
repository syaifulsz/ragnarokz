<nav class="rz-pager">
    <ul class="pager">
        @if (@$pagination['prev'])
            <li class="previous">
                <a
                    href="{{ $pagination['prev']->_url() }}"
                    class="rz-pager__btn rz-pager__btn--prev js-rz-pager__btn"
                    tabindex="0"
                    role="button"
                    data-html="true"
                    data-toggle="popover"
                    data-trigger="hover"
                    title="<span class='label label-info'>Previous Chapter</span><span class='title'>{{ $pagination['prev']->_title() }}</span>"
                    data-content="<img src='{{ $pagination['prev']->_coverThumb() }}' alt='{{ $pagination['prev']->_title() }}' class='rz-pager__thumb' width='244' height='244' />">
                    <span class="glyphicon glyphicon-menu-left"></span>
                </a>
            </li>
        @endif
        @if (@$pagination['next'])
            <li class="next">
                <a
                    href="{{ $pagination['next']->_url() }}"
                    class="rz-pager__btn rz-pager__btn--next js-rz-pager__btn js-rz-pager__btn--next"
                    tabindex="0"
                    role="button"
                    data-html="true"
                    data-toggle="popover"
                    data-trigger="hover"
                    title="<span class='label label-info'>Next Chapter</span><span class='title'>{{ $pagination['next']->_title() }}</span>"
                    data-content="<img src='{{ $pagination['next']->_coverThumb() }}' alt='{{ $pagination['next']->_title() }}' class='rz-pager__thumb' width='244' height='244' />"
                    data-placement="left">
                    <span class="glyphicon glyphicon-menu-right"></span>
                </a>
            </li>
        @endif
    </ul>
</nav>

@push('scripts')
    <script type="text/javascript">
    $('.js-rz-pager__btn').popover();
    var $nextChapter = $('.js-rz-pager__btn--next');
    var activeClass = 'rz-pager__btn--active';
    // $(window).scroll(function() {
    //     clearTimeout($.data(this, 'scrollTimer'));
    //     $.data(this, 'scrollTimer', setTimeout(function() {
    //         var $this = $(this);
    //         if ($(window).scrollTop() + $(window).height() == $(document).height()) {
    //             $nextChapter.addClass(activeClass).popover('show');
    //         } else {
    //             $nextChapter.removeClass(activeClass).popover('hide');;
    //         }
    //     }, 250));
    // });
    </script>
@endpush
