<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Message;
use App\Notifications\SendMessage;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    
    public function sendMessage(Request $request)
    {
        $data=$request->except('_token');
        $data['from_user']=auth()->id();
        $data['created_at']=$data['updated_at']=Carbon::now();
        $message=Message::insert($data);
        if($message)
        {
           User::find($request->to_user)->notify(new SendMessage($data));


           // $data['created_at']=Carbon::now()->diffForHumans();
           $data['date_time']=Carbon::now()->toDateTimeString();

            // event(new MessageEvent($data));
            broadcast(new MessageEvent($data))->toOthers();

            return response()->json(['status'=>true,'message'=>'send message was successfully.'],200);
        }
        else
        {
            return reponse()->json(['status'=>false,'message'=>'send message was not successfully.'],500);
        }
    }
    public function getUsers()
    {
        $users=User::where('id','!=',auth()->id())->pluck('name','id')->all();
        if(count($users))
            return response()->json(['status'=>true,'users'=>$users],200);
        else
            return response()->json(['status'=>false,'message'=>'No user']);
    }

    public function getNotification(Request $request)
    {
        $result=$request->user()->unreadNotifications()->paginate(10);
        $data=[];
        
        $result->filter(function($notification) use(&$data){
            $data[$notification->id]=$notification->data;
            // $data[$notification->id]['created_at']=$notification->created_at->diffForHumans();
            $data[$notification->id]['date_time']=$notification->created_at->toDateTimeString();
        });

        return response()->json($data);
    }
    public function readNotification(Request $request)
    {
        $request->user()->unreadNotifications()->paginate(10)->markAsRead();
        return response()->json(['message'=>'read notification was success.'],200);
    }
}
