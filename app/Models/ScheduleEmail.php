<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class ScheduleEmail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected static $logAttributes = [
        'schedule_time', 'url', 'locale', 'view_file', 'subject', 'message', 'email', 'status'
    ];
    protected static $logOnlyDirty = true;
}
