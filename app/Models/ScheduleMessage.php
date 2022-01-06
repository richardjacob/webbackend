<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class ScheduleMessage extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected static $logAttributes = [
        'schedule_time', 'to', 'specific_user', 'user_type', 'users', 'message_type', 'txtEditor', 'status'
    ];
    protected static $logOnlyDirty = true;
}
