<nav class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= route('home') ?>">RagnarokZ III</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ route('home') }}">Home {!! Request::is('/') ? ' <span class="sr-only">(current)</span>' : '' !!}</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::check())
                    <li class="{{ Request::is('report') ? 'active' : '' }}"><a href="{{ route('forms/report') }}">Report <span class="glyphicon glyphicon-alert"></span></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('forms/report') }}" class="js-submit-report">
                                    Submit Report
                                    <div class="text-muted"><small>Improvements, bugs and issues</small></div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logger') }}">
                                    Logger
                                    <div class="text-muted"><small>Records of errors when scrapping</small></div>
                                </a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ route('auth/logout/handle', ['redirect' => Request::url()]) }}">Logout</a></li>
                        </ul>
                    </li>
                @else
                    <li class="{{ Request::is('login') ? 'active' : '' }}"><a href="{{ route('auth/login') }}">Login{!! Request::is('auth/login') ? ' <span class="sr-only">(current)</span>' : '' !!}</a></li>
                    @if (config('auth.enableRegister'))
                        <li class="{{ Request::is('register') ? 'active' : '' }}"><a href="{{ route('auth/register') }}">Register{!! Request::is('auth/register') ? ' <span class="sr-only">(current)</span>' : ''  !!}</a></li>
                    @endif
                @endif
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>