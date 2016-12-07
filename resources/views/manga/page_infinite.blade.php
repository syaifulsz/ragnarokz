@extends('layouts.master')

@section('content')
    @include('manga._page_loop', ['pages' => $pages, 'pagination' => $pagination, 'infinite' => true]);
    @include('manga._page_inf8_toggle', ['chapter' => $chapter]);

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

    <div class="modal fade js-modal-alert" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="container-fluid">
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>{{ $manga->_title() }}</h4>
                    <p><strong>FIN.</strong> No more chapter beyond this.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">

    $(document).ready(function() {

        var $loader = $('.js-modal-loading');
        var $alert = $('.js-modal-alert');

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
                    // console.log('bottom of page');

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

                            $group.data('inf-chapter-url', $data.data('inf-chapter-url'));
                            $group.data('inf-next-chapter-order', $data.data('inf-next-chapter-order'));
                            $group.data('inf-next-chapter-url', $data.data('inf-next-chapter-url'));
                            $('.js-inf8-btn').attr('href', $data.data('inf-chapter-url'));

                            $group.html($data.html());
                            $('.js-breadcrumb').html($data.find('.js-breadcrumb').html());
                            $(window).scrollTop(50);
                            $loader.modal('hide');

                            return;
                        },
                        error: function() {
                            $loader.modal('hide');
                            $alert.modal('show');
                        }
                    });

                } else {
                    // top of page
                    // console.log('top of page');
                }
            }, 250));
        });
    });
    </script>
@endpush