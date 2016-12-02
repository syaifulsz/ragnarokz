@if ($breadcrumb)
    <div class="container">
        <ol class="breadcrumb">
            @foreach ($breadcrumb as $title => $url)
                <li><a href="{{ $url }}">{{ $title }}</a></li>
            @endforeach
        </ol>
    </div>
@endif
