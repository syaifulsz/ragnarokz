@extends('layouts.master')

@section('content')
    <div class="container">
        @include('manga._jumbotron', ['title' => $manga->_title(), 'cover' => $chapterCover, 'recents' => $recents])

        <div class="btn-toolbar md-mb-15" role="toolbar">
            <div class="btn-group" role="group">
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <strong>Sort:</strong>
                        @if (!$request->has('sort') || $request->get('sort') == 'desc')
                            Show latest chapters
                        @endif
                        @if ($request->get('sort') == 'asc')
                            Show from begining
                        @endif
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        @if (!$request->has('sort') || $request->get('sort') == 'desc')
                            <li><a href="{{ \App\Components\UrlManager::route('manga/chapter', $request->except(['page']), ['sort' => 'asc']) }}">Show from begining</a></li>
                        @endif
                        @if ($request->get('sort') == 'asc')
                            <li><a href="{{ \App\Components\UrlManager::route('manga/chapter', $request->except(['page']), ['sort' => 'desc']) }}">Show latest chapters</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ \App\Components\UrlManager::route('manga/chapter', $request->except(['page']), ['chapter-filter' => ($request->get('chapter-filter') == 'has-pages' ? null : 'has-pages')]) }}" class="btn btn-default {{ $request->get('chapter-filter') == 'has-pages' ? 'active' : '' }}">Chapter with pages only</a>
            </div>
        </div>

        @if ($chapters)
            <p>List of available chapters</p>
            <ul>
                @foreach ($chapters as $chapter)
                    <li>
                        @if ($chapter->pages->count())
                            {{ $chapter->_order() }}: <a href="{{ $chapter->_url() }}">{{ $chapter->_titleMin() }}</a>
                            <small>
                                (<a href="#" class="js-chapter-preview" data-href="{{ $chapter->_url() }}" data-chapter-title="{{ $chapter->_title() }}" data-manga-slug="{{ $manga->_slug() }}" data-chapter-slug="{{ $chapter->_slugOrder() }}">preview</a>)
                                @if ($chapter->recents->first())
                                    <span class="text-muted">- Last read on {{ $chapter->recents->first()->_recent()['time'] }}</span>
                                @endif
                            </small>
                        @else
                            <span class="text-muted">{{ $chapter->_order() }}: {{ $chapter->_titleMin() }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            {{ $chapters->links() }}
        @endif
    </div>

    <div class="modal fade model__chapter-preview js-model__chapter-preview" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.js-chapter-preview').on('click', function(e) {
                e.preventDefault();

                var $modal = $('.js-model__chapter-preview');

                $.ajax({
                    type: 'POST',
                    url: '/ajax/chapter-teaser',
                    data: {
                        manga_slug: $(this).data('manga-slug'),
                        chapter_slug: $(this).data('chapter-slug'),
                        html: true
                    },
                    success: function(data) {
                        $modal.find('.modal-title').html('').html($(this).data('data-chapter-title'));
                        $modal.find('.modal-body').html('').html(data);
                        $modal.find('.modal-body').find('img').on('click', function() {
                            window.location.href = $(this).data('data-href');
                        });
                        $modal.modal('show');
                    }
                });
            });
        });
    </script>
@endpush