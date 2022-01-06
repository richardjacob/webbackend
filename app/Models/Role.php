<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shanmuga\LaravelEntrust\Models\EntrustRole;
use DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends EntrustRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected static $logAttributes = [
        'name', 'display_name', 'description'
    ];
    protected static $logOnlyDirty = true;


    // Get permission_id in lists type
    public static function permission_role($id)
    {
        return DB::table('permission_role')->where('role_id', $id)->pluck('permission_id')->toArray();
    }

    // Get role_user data by using given id
    public static function role_user($id)
    {
        return DB::table('role_user')->where('user_id', $id)->first();
    }
}
