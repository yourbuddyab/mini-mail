<?php

use App\Events\EmailProgressUpdated;
use App\Mail\CampaignEmail;
use App\Models\Campaign;
use Illuminate\Support\Facades\Mail;
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


Route::get('/test-mail', function () {
    $cam = Campaign::find(1)->first();
    Mail::raw('This is a test email', function ($message) {
        $message->to('arjunbhati180@gmail.com')
                ->subject('Test Email');
    });

    dd(Mail::to('arjunbhati180@gmail.com')->send(new CampaignEmail(['name' => "arjun Bhati", 'contant' => $cam->contant, 'subject' => $cam->name])));
    return 'Event broadcasted';
});