<?php

// login
Route::get('/login', ['as' => 'auth/login', 'uses' => 'AuthController@login']);
Route::post('/handleLogin', ['before' => 'csrf', 'as' => 'auth/login/handle', 'uses' => 'AuthController@handleLogin']);

// logout
Route::get('/handleLogout', ['as' => 'auth/logout/handle', 'uses' => 'AuthController@handleLogout']);

// register
if (config('auth.enableRegister')) {
    Route::get('/register', ['as' => 'auth/register', 'uses' => 'AuthController@register']);
    Route::post('/handleRegister', ['before' => 'csrf', 'as' => 'auth/register/handle', 'uses' => 'AuthController@handleRegister']);
}
