<div class="container">
    <div class="progress js-rz-progress rz-progress">
        <div
            class="progress-bar progress-bar-danger"
            role="progressbar"
            data-page="0"
            data-page-count="<?= $pagesCount ?>"
            ></div>
    </div>
    <div class="rz-progress__indicator hide js-rz-progress__indicator">
        <span class="rz-progress__indicator__thumb js-rz-progress__indicator__thumb"></span>
        Chapter <?= $chapterOrder ?> Page <span class="rz-progress__indicator-page js-rz-progress__indicator-page">1</span> of <?= $pagesCount ?>
    </div>
    <div class="progress" style="height: 5px;" data-toggle="tooltip" data-placement="top" title="You have read {{ $progressChapter['read'] }} of {{ $progressChapter['total'] }} chapters. And you have {{ $progressChapter['remaining'] }} remaining chapters to read.">
        <div class="progress-bar" role="progressbar" aria-valuenow="{{ $progressChapter['read'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $progressChapter['progress'] }}%;"></div>
    </div>

    @include('manga._jumbotron', ['title' => $title, 'cover' => $chapterCover, 'recents' => $recents, 'jsMangaPage' => true])
</div>

@push('scripts')
    <script src="{{ asset('themes/default/assets/plugins/jquery-scrollspy/scrollspy.js') }}"></script>
    <script type="text/javascript">

    var $nextChapter = $('.js-rz-pager__btn--next');
    var activeClass = 'rz-pager__btn--active';

    var $progressBar = $('.js-rz-progress .progress-bar');
    var pagesCount = $progressBar.data('page-count');
    var $progressBarIndicatorContainer = $('.js-rz-progress__indicator');
    var $progressBarIndicator = $('.js-rz-progress__indicator-page');

    var $item = $('.rz-page-group__item');
    var $saveButton = $('.js-rz-manage__save-button');
    var $itemThumb = $('.js-rz-progress__indicator__thumb');

    var progressBarRev = 0;

    var $chapterProgressBar = $('.js-progress');

    $(window).scroll(function () {
        var $this = $(this);
        if (!$this.scrollTop()) {
            $progressBar.css({width: '0'});
            $progressBarIndicatorContainer.addClass('hide');
            progressBarRev = 0;
        }

        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            $progressBar.css({width: '100%'});
            progressBarRev = 100;
        }
    });

    function progressBar($this) {
        var pageId = $this.data('page-id');
        progressBarRev = (pageId/pagesCount) * 100;

        $progressBarIndicatorContainer.removeClass('rz-progress__indicator--finish');
        if (pageId == pagesCount) $progressBarIndicatorContainer.addClass('rz-progress__indicator--finish');

        var thisImg = $this.find('img').attr('src');
        $itemThumb.css('background-image', 'url(' + thisImg + ')');

        if (pageId) $progressBar.css({width: progressBarRev + '%'});

        $progressBarIndicatorContainer.addClass('hide');
        if (pageId) $progressBarIndicatorContainer.removeClass('hide');

        if ($nextChapter.length) {
            if (progressBarRev == 100) $nextChapter.addClass(activeClass).popover('show');
            if (progressBarRev < 100) $nextChapter.removeClass(activeClass).popover('hide');
        }

        $progressBarIndicator.html(pageId);
    }

    $item.on('scrollSpy:enter', function() {
        console.log('enter:', $(this).attr('id'));
        progressBar($(this));
    });

    $item.on('scrollSpy:exit', function() {
        console.log('exit:', $(this).attr('id'));
    });

    $item.scrollSpy();

    $('[data-toggle="tooltip"]').tooltip();

    </script>
@endpush
