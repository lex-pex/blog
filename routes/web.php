<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'GuestController@index')->name('articles_index');

Route::get('/category/{id}', 'GuestController@category')->name('category_index');

Route::get('/article/{id}', 'GuestController@show')->name('article_show');

Route::get('/article/{id}/edit', 'GuestController@edit')->name('article_edit');

Route::get('/home', 'HomeController@index')->middleware('moderator')->name('home');

Route::get('/profile', 'ProfileController@profile')->middleware('auth')->name('profile');

Route::resource('articles', 'ArticleController')->except(['index', 'show']);

Route::middleware('admin')->resource('categories', 'CategoryController')->except(['show']);

Route::get('error_page', function(){ return view('error'); })->name('error_page');

Auth::routes();
