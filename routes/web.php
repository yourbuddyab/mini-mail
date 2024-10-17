<?php

use App\Events\EmailProgressUpdated;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::get('/broadcast-test', function () {
    broadcast(new EmailProgressUpdated(1, 5, 10)); // Test with example values
    return 'Event broadcasted';
});