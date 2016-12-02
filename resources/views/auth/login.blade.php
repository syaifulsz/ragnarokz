@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-offset-4 col-md-4">
            @if ($request->session()->has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ $request->session()->get('message') }}
                </div>
            @endif
            @if ($errors->has('message'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ $errors->first('message') }}
                </div>
            @endif
            <form action="{{ route('auth/login/handle') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" type="email" class="form-control" id="email" value="{{ $request->old('email') }}">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password">
                </div>
                <div class="checkbox">
                    <label for="remember"><input type="checkbox" name="remember" id="remember"> Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
                @if (config('auth.enableRegister'))
                    <a href="{{ route('auth/register') }}" class="btn btn-link btn-block">I want to register</a>
                @endif
            </form>
        </div>
    </div>
@endsection
