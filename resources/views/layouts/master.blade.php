<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle or 'Index' }} - RagnarokZ III</title>
    <link href="{{ asset('themes/default/style.css') }}" rel="stylesheet">
    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('nav')
    @include('breadcrumb')
    @yield('content')
    <div class="container text-muted text-center xs-mt-20 xs-pb-20">
        <hr>
        <small>Copyright &copy; 2016 SYAIFUL SHAH ZINAN &bull; RAGNAROKZ</small>
    </div>
    <script src="{{ asset('themes/default/assets/framework/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('themes/default/assets/framework/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
