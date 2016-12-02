<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    /**
     * Login Page
     *
     * @param  Request $request
     * @return view
     */
    public function login(Request $request)
    {
        $data = [
            'request' => $request,
            'breadcrumb' => null,
        ];

        $this->breadcrumb['Login'] = route('auth/login');
        $data['breadcrumb'] = $this->breadcrumb;

        View::share($data);
        return view('auth.login', $data);
    }

    /**
     * handleLogin()
     * Functing to attemp authenticate user from login page
     *
     * @param  Request $request
     * @return mixed
     */
    public function handleLogin(Request $request)
    {
        $failRedirect = ($request->has('redirect') ? $request->get('redirect') : 'login');
        $messageError = 'Invalid ID or password, please try again.';

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|alphaNum|min:3',
        ]);

        if ($validator->fails()) {
            return redirect($failRedirect)
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $login = Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password'), 'active' => 1], $request->has('remember'));

        return $login ? redirect()->intended('/') : redirect($failRedirect)->withError($messageError);
    }

    public function register(Request $request)
    {
        if (!config('auth.enableRegister')) abort(404, 'Page not found.');
        
        $data = [
            'request' => $request
        ];

        $this->breadcrumb['Register'] = route('auth/register');
        $data['breadcrumb'] = $this->breadcrumb;

        View::share($data);
        return view('auth.register', ['request' => $request]);
    }

    public function handleRegister(Request $request)
    {
        if (!config('auth.enableRegister')) abort(404, 'Page not found.');

        $failRedirect = 'register';
        $successMessage = 'Congratulation! You\'ve successfully registered with RagnarokZ. Continue to login?';
        $errorMessage = 'Oops! Something went wrong. Please try register again later.';

        $validator = Validator::make($request->all(), [
			'name' => 'required',
			'username' => 'required|unique:users,username',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|alphaNum|min:3',
			'passwordConfirm' => 'required|alphaNum|min:3'
		]);

        if ($validator->fails()) {
            return redirect($failRedirect)
                ->withErrors($validator)
                ->withInput($request->except('password', 'passwordConfirm'));
        }

        if ($request->get('password') != $request->get('passwordConfirm')) {
			return redirect($failRedirect)
				->withErrors([
					'passwordConfirm' => 'Invalid password, please try again.'
				])
				->withInput(Input::except('password', 'passwordConfirm'));
		}

        $userCreate = User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'active' => 1,
            'password' => Hash::make($request->get('password'))
        ]);

        return $userCreate ? redirect('login')->with('message', $successMessage)->withInput($request->only('email')) : redirect($failRedirect)->withError('message', $errorMessage);
    }

    public function handleLogout(Request $request)
    {
        Auth::logout();

        return redirect()->intended(($request->has('redirect') ? $request->get('redirect') : '/'));
    }
}
