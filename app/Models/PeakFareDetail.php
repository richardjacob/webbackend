<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class PeakFareDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion;
    use  LogsActivity;

    public $timestamps = false;

    // Make Id Column as Mass Assignable
    protected $fillable = ['id', 'fare_id'];

    public $appends = ['str_day_name'];

    protected $convert_fields = ['price'];

    public $disable_admin_panel_convertion = true;


    protected static $logAttributes = [
        'fare_id', 'type', 'day', 'start_time', 'end_time', 'price'
    ];
    protected static $logOnlyDirty = true;


    // Get the Name of the Day
    public function getStrDayNameAttribute()
    {
        $day_names = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return $day_names[($this->attributes['day'] or '0')];
    }
}
