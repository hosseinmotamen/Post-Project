<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\AutheticatedUser;

Route::middleware('AutheticatedUser')->group(function () {

	Route::post('user/post', 'App\Http\Controllers\Api\PostController@create');
	Route::get('user/post/{id}', 'App\Http\Controllers\Api\PostController@show');
	Route::put('user/post/{id}', 'App\Http\Controllers\Api\PostController@update');
	Route::delete('user/post{id}', 'App\Http\Controllers\Api\PostController@destroy');
});
