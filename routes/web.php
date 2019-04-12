<?php

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('logout',['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

// google login
Route::get('login/google', 'GoogleAuthController@redirect')->middleware('guest')->name('google.sign-in');
Route::get('login/google/callback', 'GoogleAuthController@callback');

// only authorized routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);
    Route::get('/spreadsheet', ['uses' => 'SpreadsheetController@index']);
    Route::post('/sheet', ['as'=>'sheet.store','uses' => 'SheetController@store']);
    Route::delete('/sheet/delete', ['as' => 'sheet.delete', 'uses' => 'SheetController@delete']);
    Route::delete('/spreadsheet/delete', ['as' => 'spreadsheet.delete', 'uses' => 'SpreadsheetController@delete']);
    Route::post('/spreadsheet', ['as' => 'spreadsheet.store', 'uses' => 'SpreadsheetController@store']);
    Route::get('/spreadsheet/{spreadsheet_id}/{sheet?}', ['as' => 'spreadsheet.edit', 'uses' => 'SpreadsheetController@edit']);
    Route::post('/spreadsheet/{spreadsheet_id}/{sheet}', ['as' => 'spreadsheet.update', 'uses' => 'SpreadsheetController@update']);
});
