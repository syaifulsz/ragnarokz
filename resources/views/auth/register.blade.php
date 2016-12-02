@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <form action="{{ route('auth/register/handle') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Name</label>
                    <input name="name" type="name" class="form-control" id="name" value="{{ $request->old('name') }}">
                    @if ($errors->has('name'))
                        <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                    <label for="username">Username</label>
                    <input name="username" type="username" class="form-control" id="username" value="{{ $request->old('username') }}">
                    @if ($errors->has('username'))
                        <span class="help-block">{{ $errors->first('username') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">Email</label>
                    <input name="email" type="email" class="form-control" id="email" value="{{ $request->old('email') }}">
                    @if ($errors->has('email'))
                        <span class="help-block">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('passwordConfirm') || $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password">
                    @if ($errors->has('password'))
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('passwordConfirm') ? 'has-error' : '' }}">
                    <label for="passwordConfirm">Confirm Password</label>
                    <input name="passwordConfirm" type="password" class="form-control" id="passwordConfirm">
                    @if ($errors->has('passwordConfirm'))
                        <span class="help-block">{{ $errors->first('passwordConfirm') }}</span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
        </div>
    </div>
@endsection
