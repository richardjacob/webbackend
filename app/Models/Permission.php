<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shanmuga\LaravelEntrust\Models\EntrustPermission;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends EntrustPermission implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected static $logAttributes = [
        'name', 'display_name', 'description', 'menu_name'
    ];
    protected static $logOnlyDirty = true;
}
