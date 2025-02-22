<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});



Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return \App\Models\group_members::where('chat_id', $chatId)
        ->where('user_id', $user->id)
        ->exists();
});


Broadcast::channel('online-users', function ($user) {
    return $user ? ['id' => $user->id, 'name' => $user->name] : false;
});


Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return \App\Models\group_members::where('chat_id', $chatId)
        ->where('user_id', $user->id)
        ->exists();
});
