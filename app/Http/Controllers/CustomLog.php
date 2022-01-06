<?php

/**
 * Company Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Company
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use DB;

class CustomLog extends Controller
{
    public function __construct()
    {
       DB::enableQueryLog();
    }

    public static function info($msg){
        $logs = DB::getQueryLog();
        $time = 0;
        foreach($logs as $log){
            $time+=$log['time'];
        }
        Log::info($msg.", Total Time:".$time."ms", $logs);
    }
}