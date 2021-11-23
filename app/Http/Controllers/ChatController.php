<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\ChatMessage;
use App\Models\User;
use App\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Controller constructor.
     *
     * @param  \App\Accounts  $accounts
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;

    }

    /**
     * Get all the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $auth_user = $this->auth->getAuthenticatedUser();
        $user = $auth_user['data'];
        $user_id = $user['id'];
        $chats = Chat::select('chat.id','to.name',DB::raw('DATE_FORMAT(`chat_message`.created_at, "%H:%i:%s %d-%m-%Y") as last'))->join('chat_member','chat_member.chat_id','chat.id')->join('chat_message','chat_message.chat_id','chat.id')->where('chat_member.user_id',$user['id'])->orderBy('chat_message.created_at', 'DESC')->groupBy('chat.id')
        ->join('chat_member as to', function($join) use ($user_id)
                         {
                             $join->on('chat_member.chat_id', '=', 'to.chat_id')->where('to.user_id','!=',$user_id);
                         })->with('messages')
        ->get();
        $data['code'] = 200; 
        $data['message'] = "success"; 
        $data['data'] = $chats; 

        return response()->json($data, 200);
    }

    /**
     * Store a user.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send_message(Request $request): JsonResponse
    {
        $auth_user = $this->auth->getAuthenticatedUser();
        $user = $auth_user['data'];
        $to = User::where('id',$request->to)->first();
        if(!$to){
            return response()->json([
                'status' => 'failed',
                'code' => 400,
                'message' => "receiver not found"
            ], 400);
        }
        if($user['id'] == $request->to){
            return response()->json([
                'status' => 'failed',
                'code' => 400,
                'message' => "cannot send message to yourself"
            ], 400);
        }
        DB::beginTransaction();
        try {
        /*check if sender already in chat room*/
        /*check if receiver in the same room*/
            $check = collect(\DB::select("SELECT c.id
                    FROM chat c
                    JOIN chat_member cm ON c.id = cm.chat_id AND cm.user_id = {$user['id']}
                    JOIN chat_member cm2 ON c.id = cm2.chat_id AND cm2.user_id = {$request->to}"))->first();

            if($check){
                $chatMessage = New ChatMessage();
                $chatMessage->user_id = $user['id']; 
                $chatMessage->chat_id = $check->id; 
                $chatMessage->message = $request->message; 
                $chatMessage->save();
            }else{
                $chat = New Chat();
                $chat->save();

                $chatMember1 = New ChatMember();
                $chatMember1->user_id = $user['id'];
                $chatMember1->chat_id = $chat->id;
                $chatMember1->email = $user['email'];
                $chatMember1->name = $user['name'];
                $chatMember1->save();

                $chatMember2 = New ChatMember();
                $chatMember2->user_id = $to->id;
                $chatMember2->chat_id = $chat->id;
                $chatMember2->email = $to->email;
                $chatMember2->name = $to->name;
                $chatMember2->save();


                $chatMessage = New ChatMessage();
                $chatMessage->user_id = $user['id']; 
                $chatMessage->chat_id = $chat->id; 
                $chatMessage->message = $request->message; 
                $chatMessage->save();   
            }
        
        
            DB::commit();                

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'chat send successfully'
            ], 200);
            
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'code' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
        
        // $request->input('to');



        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Get a user.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // dd($id);
        $auth_user = $this->auth->getAuthenticatedUser();
        $user = $auth_user['data'];
        $user_id = $user['id'];
        $chats = Chat::select('chat.id','to.name',DB::raw('DATE_FORMAT(`chat_message`.created_at, "%H:%i:%s %d-%m-%Y") as last'))->join('chat_member','chat_member.chat_id','chat.id')->join('chat_message','chat_message.chat_id','chat.id')->orderBy('chat_message.created_at', 'DESC')->groupBy('chat.id')
        ->join('chat_member as to', function($join) use ($user_id)
                         {
                             $join->on('chat_member.chat_id', '=', 'to.chat_id')->where('to.user_id','!=',$user_id);
                         })->with('messages')->where('chat.id',$id)
        ->first();
        /*update data*/
        $chatMessage = ChatMessage::where('chat_id',$chats->id)->update(['status' => 1]);

        $data['code'] = 200; 
        $data['message'] = "success"; 
        $data['data'] = $chats; 

        return response()->json($data, 200);
    }

    public function reply(Request $request, int $id): JsonResponse
    {
        $auth_user = $this->auth->getAuthenticatedUser();
        $user = $auth_user['data'];
        DB::beginTransaction();
        try {
        /*check if sender already in chat room*/
        /*check if receiver in the same room*/
            $chatMessage = New ChatMessage();
            $chatMessage->user_id = $user['id']; 
            $chatMessage->chat_id = $id;
            $chatMessage->message = $request->message; 
            $chatMessage->save();
        
        
            DB::commit();                

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'chat send successfully'
            ], 200);
            
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'code' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    
}
