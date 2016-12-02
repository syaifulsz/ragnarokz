@extends('layouts.master')

@section('content')
    @include('manga._page_header', ['title' => $chapter->_title(), 'cover' => $chapterCover, 'recents' => $recents, 'pagesCount' => $pages->count(), 'chapterOrder' => $chapter->_order()])
    @include('manga._page_loop', ['pages' => $pages, 'pagination' => $pagination, 'infinite' => true]);
    @include('manga._page_pager', ['pagination' => $pagination])

    <div class="container">
        <div class="alert alert-danger xs-mt-15">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Fin.</strong> You have finish reading chapter {{ $chapter->_order() }}.
        </div>
        @include('manga._jumbotron', ['title' => $chapter->_title(), 'cover' => $chapterCover, 'recents' => $recents])
    </div>
@endsection
