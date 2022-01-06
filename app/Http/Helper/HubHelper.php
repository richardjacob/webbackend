<?php
namespace App\Http\Helper;

use DB;
use Auth;
use Route;

class HubHelper
{
	public function check_permission()
	{
		$user = Auth::guard('hub')->user();

        $permission_role = DB::table('permission_role')->where('role_id', $user->role_id)->pluck('permission_id')->toArray();

        $routes = array();
        foreach($permission_role as $role_id){
            $permission_role = DB::table('permissions')->where('id', $role_id)->pluck('name')->first();
            $routes[] = $permission_role;
        }

        $current_route = str_replace('hub/', '', Route::current()->uri());

        if(!in_array($current_route, $routes)) {
            echo "<a href='dashboard'>Go to Dashboard</a>";
        }
        else return '1';
	}
}
