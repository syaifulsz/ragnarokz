@extends('layouts.master')

@section('content')
    @include('manga._page_loop', ['pages' => $pages, 'pagination' => $pagination, 'infinite' => true]);
    <div class="modal fade js-modal-loading" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="sk-folding-cube">
                <div class="sk-cube1 sk-cube"></div>
                <div class="sk-cube2 sk-cube"></div>
                <div class="sk-cube4 sk-cube"></div>
                <div class="sk-cube3 sk-cube"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">

    $(document).ready(function() {

        var $loader = $('.js-modal-loading');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(window).scroll(function() {
            clearTimeout($.data(this, 'scrollTimer'));
            $.data(this, 'scrollTimer', setTimeout(function() {
                var $this = $(this);

                var $group = $(document).find('.js-rz-page-group');

                if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                    // bottom of page
                    console.log('bottom of page');

                    $loader.modal('show');

                    $.ajax({
                        type: 'POST',
                        url: '/ajax/get-page',
                        data: {
                            manga_slug: $group.data('inf-manga-slug'),
                            chapter_slug: $group.data('inf-next-chapter-order'),
                            html: true
                        },
                        success: function(data) {

                            $.ajax({
                                type: 'POST',
                                url: '/ajax/set-recent',
                                data: {
                                    manga_slug: $group.data('inf-manga-slug'),
                                    chapter_slug: $group.data('inf-next-chapter-order'),
                                }
                            });

                            var $data = $(data);
                            history.pushState({}, '', $group.data('inf-next-chapter-url'));
                            $group.data('inf-next-chapter-order', $data.data('inf-next-chapter-order'));
                            $group.data('inf-next-chapter-url', $data.data('inf-next-chapter-url'));
                            $group.html($data.html());
                            $('.js-breadcrumb').html($data.find('.js-breadcrumb').html());
                            $(window).scrollTop(50);
                            $loader.modal('hide');

                            return;
                        }
                    });

                } else {
                    // top of page
                    console.log('top of page');
                }
            }, 250));
        });
    });
    </script>
@endpush