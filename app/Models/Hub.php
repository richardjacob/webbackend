<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Hub extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Notifiable, LogsActivity;
    use LaravelEntrustUserTrait;

    protected $table = 'hubs';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name', 'address', 'status',
    ];

    protected static $logAttributes = [
        'name', ' address', 'status'
    ];
    protected static $logOnlyDirty = true;
}
