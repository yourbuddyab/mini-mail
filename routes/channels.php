<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('campaign/emails-count/{campaign}', [CampaignController::class, 'saveCount']);


Broadcast::channel('campaign/send-count/{campaign}', [CampaignController::class, 'sendCount']);