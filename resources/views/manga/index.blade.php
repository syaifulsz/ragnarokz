@extends('layouts.master')

@section('content')
    <div class="container">
        @if (Auth::check())
            <p>Welcome, <strong>{{ Auth::user()->name }}</strong></p>
        @endif

        <p>List of available Mangas</p>
        <ul>
            @foreach ($mangas as $manga)
                <li><a href="{{ $manga->_url() }}">{{ $manga->_title() }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection
