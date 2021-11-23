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
        $chats = ChatMember::where('user_id',$user['id'])->with('chat')->get();
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
            return response()->json([], 400);
        }
        /*check if user already in chat room*/

        $check = Chat::join('chat_member', 'chat.id', '=', 'chat_id')
                    ->where('user_id',$user['id'])
                    ->get();
        // dd($check);
        if(count($check)>0){
            // $check = $check;
            $idx = is_array($check);

            dd($idx);
            if($idx){
                dd($idx);
            }else{
                DB::beginTransaction();
                try {
                    
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
    
                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'chat send successfully'
                    ], 400);
                    
                } catch (Exception $e) {
                    DB::rollback();
    
                    return response()->json([
                        'status' => 'failed',
                        'code' => 400,
                        'message' => $e->getMessage()
                    ], 400);
                }

            }
        }else{
            DB::beginTransaction();
            try {
                
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

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'chat send successfully'
                ], 400);
                
            } catch (Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => 'failed',
                    'code' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }

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
        $user = $this->accounts->getUserById($id);

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Update a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->accounts->updateUserById($id, $request->all());

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Delete a user.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->accounts->deleteUserById($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
