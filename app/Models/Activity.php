<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class Activity extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use LogsActivity;

    protected static $logAttributes = [
        'user_type', 'user_id'
    ];
    protected static $logOnlyDirty = true;
}
