<?php



namespace App\Http\Controllers;

// require 'vendor/autoload.php';
use Mailgun;
use DB;
use Illuminate\Support\Facades\Log;

use JWTAuth;
use App\Models\Comment;
use App\Models\User;

class TestEmail extends Controller
{
    public function __construct()
    {
        $this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
    }

    public function trip_comment()
    {
        Log::info("trip_comment Api Stp:1 :");
        $user_details = JWTAuth::parseToken()->authenticate();
        $comment = Comment::active()->where('comment_by',$user_details->user_type)->get();

        CustomLog::info("trip_comment Api Stp:2 :");
        return response()->json([
            'status_code'    => '1',
            'status_message' => "Success",
            'trip_comment' => $comment,
        ]);

    }


    public function index()
    {   
        echo "x";
        //$user = User::where('id',10431)->first();
        //$this->bonus_helper->driver_signup_bonus($user);
        //$this->bonus_helper->adjust_driver_signup_bonus($user);
        
        //$this->bonus_helper->driver_trip_bonus($user);
        //$this->bonus_helper->adjust_driver_trip_bonus($user);

        //$this->bonus_helper->driver_referral_bonus($user);
        //$this->bonus_helper->adjust_driver_referral_bonus($user);

        //$this->bonus_helper->rider_referral_bonus($user);
        //$this->bonus_helper->adjust_rider_referral_bonus($user);

        //$this->bonus_helper->rider_cashback1($user);
        //$this->bonus_helper->adjust_rider_cashback1($user);

        //$this->bonus_helper->rider_cashback2($user);
        //$this->bonus_helper->adjust_rider_cashback2($user);


        // DB::enableQueryLog();
        // $list = DB::select("SELECT * FROM comments");
        

        // $logs = DB::getQueryLog();
        // $time = 0;
        // foreach($logs as $log){
        //     $time+=$log['time'];
        // }
        // Log::info(" Total Time:".$time."ms", $logs);
        // print_r($list);


        // # Instantiate the client.
        // $mgClient = new Mailgun('3c30b26d1ccdad82415277c1eb1c7a97-1f1bd6a9-1116e9b5');
        // $domain = "alesharide.net";
        // # Make the call to the client.
        // $result = $mgClient->sendMessage($domain, array(
        //     'from'  => 'Excited User <alesharide.net>',
        //     'to'    => 'Baz <mtaslim@gmail.com>',
        //     'subject' => 'Hello',
        //     'text'  => 'Testing some Mailgun awesomness!'
        // ));
    }
   
        
    
    
    
    
}