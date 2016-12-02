<div class="jumbotron rz-jumbotron {{ @$jsMangaPage ? 'js-rz-jumbotron' : '' }}">
    @if (@$cover)
        <div class="rz-jumbotron__cover" style="background-image: url('{{ $cover }}')"></div>
    @endif
    <h1>{{ $title }}</h1>
    @if (@$recents)
        @if (@$recents['first'])
            <p>{{ $recents['first']['order'] }}: <a href="{{ $recents['first']['url'] }}">{{ $recents['first']['title'] }}</a> <span class="small text-muted">{{ $recents['first']['time'] }}</span></p>
        @endif
        @if (@$recents['recents'])
            <ul>
                @foreach ($recents['recents'] as $recent)
                    <li>{{ $recent['order'] }}: <a href="{{ $recent['url'] }}">{{ $recent['title'] }}</a> <span class="small text-muted">{{ $recent['time'] }}</span></li>
                @endforeach
            </ul>
        @endif
    @endif
</div>

@if (@$jsMangaPage)
    @push('scripts')
        <script type="text/javascript">

        if ($('.js-rz-jumbotron').length) {
            var windowHeight = $(window).height();
            var jumbotronHeight = $('.js-rz-jumbotron').offset().top;
            $('.js-rz-jumbotron').css({
                minHeight: (windowHeight - jumbotronHeight - 20) + 'px'
            });
        }

        </script>
    @endpush
@endif
