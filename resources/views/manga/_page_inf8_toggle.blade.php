<div class="inf8-nav">
    <a href="{{ Request::is($chapter->_isInf8()) ? $chapter->_url() : $chapter->_inf8() }}" class="inf8-btn js-inf8-btn {{ Request::is($chapter->_isInf8()) ? 'inf8-btn--active' : '' }}">inf8</a>
</div>