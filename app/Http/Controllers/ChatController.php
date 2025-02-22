<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Events\SendMessage;
use App\Models\User;
use App\Models\Chat;
use App\Models\MessageFile;

use App\Events\PresenceMessageEvent;

use App\Events\SendGroupMessage;
use app\Events\MessageRead;

use Illuminate\Support\Facades\DB;

use App\Models\group_members;
use Illuminate\Support\Facades\Crypt;
class ChatController extends Controller
{

  

    // new chat group

    public function newChatGrop(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([ 
            'type' => 'required|string',
            'name' => 'nullable|string',
            'group_avatar' => ['nullable', 'image', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'created_by' => 'required|exists:users,id',
            'users' => 'required|array',
            'users.*' => 'exists:users,id', 
        ]);
    
        // Handle group avatar upload
        if ($request->hasFile('group_avatar')) {
            $groupAvatarPath = $request->file('group_avatar')->store('group_avatars', 'public');
        } else {
            $groupAvatarPath = null;
        }
    
        $chat = Chat::create([
            'type' => $request->type,
            'name' => $request->name,
            'created_by' => $user->id,
            'group_avatar' => $groupAvatarPath
        ]);
    
        // Add group members if the chat is a group
        if ($request->type == 'group') {

            
            foreach ($request->users as $user_id) {

                $userRole = ($user_id == $user->id) ? 'admin' : 'member';

                group_members::create([
                    'chat_id' => $chat->id,  
                    'user_id' => $user_id,
                    'role' => $userRole,
                   
                ]);
            }

        }
    
        return response()->json(['message' => 'Chat Group created successfully', 'data' => $chat ]);
    }








    // fetch all groups

    public function getChatGroups()
    {
        $chats = Chat::where('chats.type', 'group')
            ->select(
                'chats.id', 'chats.name', 'chats.created_by', 'chats.group_avatar',
                \DB::raw('(SELECT content FROM messages WHERE chat_id = chats.id ORDER BY created_at DESC LIMIT 1) as latest_message'),
                \DB::raw('(SELECT created_at FROM messages WHERE chat_id = chats.id ORDER BY created_at DESC LIMIT 1) as latest_message_time'),
                \DB::raw('(SELECT message_type FROM messages WHERE chat_id = chats.id ORDER BY created_at DESC LIMIT 1) as latest_message_type'),
                \DB::raw('(SELECT COUNT(*) FROM messages WHERE chat_id = chats.id AND status = "unread") as unread_count')
            )
            ->with(['members' => function ($query) {
                $query->select('group_members.chat_id', 'group_members.user_id', 'group_members.role')
                    ->join('users', 'users.id', '=', 'group_members.user_id')
                    ->addSelect('users.name as user_name');
            }])
            ->get();
    
        // Process each chat to check if the latest message is a file
        foreach ($chats as $chat) {
            if (!empty($chat->latest_message)) {
                try {
                    $chat->latest_message = Crypt::decryptString($chat->latest_message);
                } catch (\Exception $e) {
                    $chat->latest_message = "Decryption error"; // Handle decryption error
                }
            }
        
            if ($chat->latest_message_type === 'file') {
                $latestMessageId = Message::where('chat_id', $chat->id)->latest()->value('id');
                
                if ($latestMessageId) {
                    $fileDetails = MessageFile::where('message_id', $latestMessageId)->first();
                    
                    if ($fileDetails) {
                        $chat->file_details = [
                            'file_name' => $fileDetails->file_name,
                            'file_path' => $fileDetails->file_path,
                            'file_type' => $fileDetails->file_type,
                            'file_size' => $fileDetails->file_size,
                        ];
                    }
                }
            }
        }
    
        return response()->json($chats);
    }
    
    
    // store group coversation


    public function groupMessageStore(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([

            'chat_id' => 'required|exists:chats,id' ,
            'content' => 'required|string',
          
            'status' => 'required|string',
            'message_type' => 'required|string',
            
        ]);
    
       
        $message = Message::create([

            'chat_id' => $request->chat_id, 
            'sender_id' => $user->id, 
           
            'content' => $request->content,
            'status' => $request->status,
            'message_type' => $request->message_type,
        ]);


        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('message_files', 'public');
    
        
            $messageFile = MessageFile::create([
                'message_id' => $message->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
    
    
              $message->file = [
                'file_name' => $messageFile->file_name,
                'file_path' =>  $messageFile->file_path,
                'file_type' => $messageFile->file_type,
                'file_size' => $messageFile->file_size,
            ];
        }

        $groupMembers = group_members::where('chat_id', $request->chat_id)->get(['user_id', 'role']);

        broadcast(new SendGroupMessage($message, $user, $groupMembers ))->toOthers();
   
    
        return response()->json(['message' => 'Message sent successfully', 'data' => $message]);
    }


    // fetch group messages 
    public function fetchGroupMessages($chat_id)
{
    $messages = Message::where('messages.chat_id', $chat_id)
        ->leftJoin('users', 'users.id', '=', 'messages.sender_id')
        ->leftJoin('group_members', function ($join) use ($chat_id) {
            $join->on('messages.sender_id', '=', 'group_members.user_id')
                 ->where('group_members.chat_id', '=', $chat_id);
        })

        
        ->select(
            'messages.*',
            'users.name as sender_name',
            'group_members.role',
            'users.avatar',

            'message_files.file_name',
            'message_files.file_path',
            'message_files.file_size',
            'message_files.file_type'
        )
        ->distinct()

        ->leftjoin('message_files', 'messages.id' , '=' , 'message_files.message_id')

        ->orderBy('messages.created_at', 'asc')
        ->get();

    return response()->json(['messages' => $messages]);
}

    
    
    // fetch selected group 

    public function fetchSelectedGroup($chat_id)
    {
       
        $messages = Chat::select('chats.*', 'group_members.user_id', 'group_members.role', 'users.name as user_name', 'users.avatar as user_avatar')
            ->where('chats.id', $chat_id)
            ->leftJoin('group_members', 'group_members.chat_id', '=', 'chats.id')
            ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
            ->get();
    
        $groupData = $messages->groupBy('id')->map(function($group) {
            return $group->first()->only(['id', 'type', 'name', 'created_by', 'group_avatar', 'created_at', 'updated_at']) + [
                'members' => $group->map(function($user) {
                    return [
                        'user_id' => $user->user_id,
                        'user_name' => $user->user_name,
                        'user_avatar' => $user->user_avatar,
                        'role' => $user->role,  
                    ];
                })
            ];
        })->values()->first();
    
        return response()->json(['data' => $groupData]);
    }




    // private message section 



    // get all users 
    
    public  function getAllUsers(){

        return response()->json(User::all());
        
    }

    // new private chat 

    public function NewChatPrivate(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([
            'type' => 'required|string',
            'name' => 'nullable|string',
            'group_avatar' => ['nullable', 'image', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'created_by' => 'required|exists:users,id',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);
    
       
        if (count($request->users) !== 2) {
            return response()->json(['message' => 'Private chat must have exactly two users'], 400);
        }
    
     
        $userIds = $request->users;
        sort($userIds); 
    
       
        $existingChat = Chat::where('type', 'private')
            ->whereHas('members', function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            }, '=', 2) 
            ->first();
    
        if ($existingChat) {
            return response()->json([
                'message' => 'Chat already exists', 
                'data' => $existingChat
            ]);
        }
    
       
        $chat = Chat::create([
            'type' => $request->type,
            'created_by' => $user->id,
        ]);
    
        foreach ($userIds as $userId) {
            group_members::create([
                'chat_id' => $chat->id,
                'user_id' => $userId,
                'role' => 'member',
            ]);
        }
    
        return response()->json([
            'message' => 'Chat created successfully',
            'data' => $chat
        ]);
    }







    // fetch private messages with chat id

    public function getPrivateMessages($chat_id){

        return Message::Select('messages.*' , 
        'message_files.file_name', 
        'message_files.file_path' ,
        'message_files.file_type',
        'message_files.file_size')
        ->where('messages.chat_id' , $chat_id)
        ->leftjoin('message_files', 'messages.id' , '=' , 'message_files.message_id')

        ->orderBy('created_at', 'asc')
        ->get();
    }


  
    public function privateChatList()
    {
        $authUserId = auth()->id(); 
    
      
        $chats = Chat::select(
                'chats.id',
                'chats.type',
                'chats.created_by',
                'chats.group_avatar',
                'chats.created_at',
                'chats.updated_at'
            )
            ->where('chats.type', 'private')
            ->leftJoin('group_members', 'group_members.chat_id', '=', 'chats.id')
            ->groupBy('chats.id', 'chats.type', 'chats.created_by', 'chats.group_avatar', 'chats.created_at', 'chats.updated_at')
            ->get();
    
        
        $filteredChats = $chats->map(function ($chat) use ($authUserId) {
            
            $userIds = group_members::where('chat_id', $chat->id)
                ->pluck('user_id')
                ->unique()
                ->toArray();
    
           
            if (!in_array($authUserId, $userIds)) {
                return null; 
            }
    
           
            $userIds = array_diff($userIds, [$authUserId]);
    
           
            $otherUser = User::whereIn('id', $userIds)->first();
            if (!$otherUser) {
                return null; // Skip if no other user exists
            }
    
        
            $chat->user = $otherUser->name;
            $chat->user_avatar = $otherUser->avatar;
    
          
            $latestMessage = Message::where('chat_id', $chat->id)->latest()->first();
    
            if (!$latestMessage) {
                return null; 
            }
    

            if ($latestMessage->message_type === 'file') {
                $fileDetails = MessageFile::where('message_id', $latestMessage->id)->first();
    
                if ($fileDetails) {
                    $latestMessage->file_details = [
                        'file_name' => $fileDetails->file_name,
                        'file_path' => $fileDetails->file_path,
                        'file_type' => $fileDetails->file_type,
                        'file_size' => $fileDetails->file_size,
                    ];
                }
            }
    
            $chat->latest_message = $latestMessage;
    
           
            $unreadCount = Message::where('chat_id', $chat->id)
                ->where('status', 'unread')
                ->count();
    
            $chat->unread_count = $unreadCount; 
    
            return $chat;
        })->filter(); 
    
        return $filteredChats->values(); 
    }
    




    
    public function privateChatDetails($chatId)
    {
        $authUserId = auth()->id(); 
    
        return group_members::select('group_members.user_id', 'users.name', 'users.avatar')
            ->where('group_members.chat_id', $chatId)
            ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
            ->where('group_members.user_id', '!=', $authUserId) 
            ->first(); 
    }
    



    public function privateMessageStore(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([
           
            'chat_id' => 'required|exists:chats,id' ,
            'content' => 'required|string',
       
            'status' => 'required|string',
            'message_type' => 'required|string',
            'file' => ['nullable', 'file', 'mimes:jpg,png,jpeg,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt', 'max:10240'], 

        ]);

            $message = Message::create([

                'chat_id' => $request->chat_id,
                'sender_id' => $user->id, 

                'content' => $request->content,
                'status' => $request->status,
                'message_type' => $request->message_type,
            ]);



         if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filePath = $file->store('message_files', 'public');

       
        $messageFile = MessageFile::create([
            'message_id' => $message->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);


         
          $message->file = [
            'file_name' => $messageFile->file_name,
            'file_path' =>  $messageFile->file_path,
            'file_type' => $messageFile->file_type,
            'file_size' => $messageFile->file_size,
        ];
    }

     

        broadcast(new SendMessage(auth()->user(), $message))->toOthers();
  
    
        return response()->json(['message' => 'Message sent successfully', 'data' => $message]);
    }




    // update private message status 

    public function markAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        Message::whereIn('id', $request->message_ids)
            ->update(['status' => 'read']);



        return response()->json(['message' => 'Messages marked as read successfully']);
    }






    
}
