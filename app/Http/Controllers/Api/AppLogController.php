<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;
use DB;

class AppLogController extends Controller
{
    public function __construct()
    {
        DB::enableQueryLog();
    }

    public function app_log_save(Request $request)
    {
        Log::info("app_log_save Stp:1 :", $request->all());

        $AppLog = new AppLog;
        $AppLog->log_type = $request->log_type ?? "";
        $AppLog->user_id  = $request->user_id ?? "";
        $AppLog->action_name = $request->action_name ?? "";
        $AppLog->detail = $request->detail ?? "";
        $AppLog->page_name = $request->page_name ?? "";
        $AppLog->button_name = $request->button_name ?? "";
        $AppLog->comment = $request->comment ?? "";
        $AppLog->phone_full_info = $request->phone_full_info ?? "";
        $AppLog->platform = $request->platform ?? "";
        $AppLog->app_version = $request->app_version ?? "";
        $AppLog->is_debug = $request->is_debug ?? "";
        $AppLog->trip_id = $request->trip_id ?? "";
        $AppLog->token_login_auth = $request->token_login_auth ?? "";
        $AppLog->token_firebase = $request->token_firebase ?? "";
        $AppLog->server_date_time = $request->server_date_time ?? "";

        if ($AppLog->save()) {
            CustomLog::info("app_log_save Stp:2 :");
            return response()->json([
                'status' => 'true',
                'status_message' => 'log saved successfully',
            ]);
        } else {
            CustomLog::info("app_log_save Stp:3 :");
            return response()->json([
                'status' => 'false',
                'status_message' => 'log Not saved',
            ]);
        }
    }



    public function app_log(Request $request)
    {
        Log::info("app_log Stp:1 :", $request->all());
        $page = $request->page ?? 1;
        $take = 15;
        $skip = ($take * $page) - $take;
        $AppLog = AppLog::skip($skip)
            ->take($take)
            ->orderBy('id', 'DESC')
            ->get();
        if ($AppLog->count() > 0) {
            CustomLog::info("app_log Stp:2 :");
            return response()->json([
                'status' =>  "true",
                'response' => $AppLog,
            ]);
        } else {
            CustomLog::info("app_log Stp:3 :");
            return response()->json([
                'status' =>  "true",
                'response' => "NO Log Found",
            ]);
        }
    }


    public function app_log_search(Request $request)
    {
        // echo $request->search_term;
        // exit;
        Log::info("app_log_search Stp:1 :", $request->all());
        $AppLog = AppLog::query()
            ->where('log_type', 'LIKE', "%{$request->search_term}%")
            ->orWhere('user_id', 'LIKE', "%{$request->search_term}%")
            ->orWhere('action_name', 'LIKE', "%{$request->search_term}%")
            ->orWhere('detail', 'LIKE', "%{$request->search_term}%")
            ->orWhere('page_name', 'LIKE', "%{$request->search_term}%")
            ->orWhere('button_name', 'LIKE', "%{$request->search_term}%")
            ->orWhere('comment', 'LIKE', "%{$request->search_term}%")
            ->orWhere('phone_full_info', 'LIKE', "%{$request->search_term}%")
            ->orWhere('platform', 'LIKE', "%{$request->search_term}%")
            ->orWhere('app_version', 'LIKE', "%{$request->search_term}%")
            ->orWhere('is_debug', 'LIKE', "%{$request->search_term}%")
            ->orWhere('trip_id', 'LIKE', "%{$request->search_term}%")
            ->orWhere('token_login_auth', 'LIKE', "%{$request->search_term}%")
            ->orWhere('token_firebase', 'LIKE', "%{$request->search_term}%")
            ->orWhere('server_date_time', 'LIKE', "%{$request->search_term}%")
            ->get();


        if ($AppLog->count() > 0) {
            CustomLog::info("app_log_search Stp:2 :");
            return response()->json([
                'status' =>  "true",
                'response' => $AppLog,
            ]);
        } else {
            CustomLog::info("app_log Stp:3 :");
            return response()->json([
                'status' =>  "true",
                'response' => "NO Searched Log Found",
            ]);
        }
    }
}
