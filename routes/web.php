<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ChatController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/chat', function () {
    return Inertia::render('Chat/Chat');
})->middleware(['auth', 'verified'])->name('chat');



// Route::get('/group', function () {
//     return Inertia::render('Chat/Group');
// })->middleware(['auth', 'verified'])->name('chat');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // chat application section 

    Route::get('all-users', [ChatController::class, 'getAllUsers'])->name('getAllUsers');

    // group chat section 
    Route::post('new-group', [ChatController::class, 'newChatGrop'])->name('newChatGrop');
    Route::get('all-groups', [ChatController::class, 'getChatGroups'])->name('getChatGroups');
    Route::post('group-message', [ChatController::class, 'groupMessageStore'])->name('groupMessageStore');
    Route::get('messages/group/{id}', [ChatController::class, 'fetchGroupMessages'])->name('fetchGroupMessages');
    Route::get('group/{id}', [ChatController::class, 'fetchSelectedGroup'])->name('fetchSelectedGroup');
    

    // private chat
    Route::post('new-private', [ChatController::class, 'NewChatPrivate'])->name('NewChatPrivate');
    Route::post('private-messages', [ChatController::class, 'privateMessageStore'])->name('privateMessageStore');
    Route::get('private-messages/{id}', [ChatController::class, 'getPrivateMessages'])->name('getPrivateMessages');
    Route::put('/messages/read', [ChatController::class, 'markAsRead']);
    Route::get('private-chat-list', [ChatController::class, 'privateChatList'])->name('privateChatList');
    Route::get('private-chat-user/{id}', [ChatController::class, 'privateChatDetails'])->name('privateChatDetails');
    
});

require __DIR__.'/auth.php';
