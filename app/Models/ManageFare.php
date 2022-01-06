<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class ManageFare extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion, LogsActivity;

    public $timestamps = false;
    public $table = 'manage_fare';

    protected $convert_fields = ['base_fare', 'min_fare', 'per_min', 'per_km', 'schedule_fare', 'schedule_cancel_fare', 'waiting_charge'];

    public $disable_admin_panel_convertion = true;

    protected static $logAttributes = [
        'location_id', ' vehicle_id', 'base_fare', 'capacity', 'min_fare', 'per_min', 'per_km', 'schedule_fare', 'schedule_cancel_fare', 'waiting_time',
        'waiting_charge', 'currency_code', 'apply_peak', 'apply_night'
    ];
    protected static $logOnlyDirty = true;




    // Join with Locations table table
    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    // Join with Car Type table table
    public function car_type()
    {
        return $this->belongsTo('App\Models\CarType', 'vehicle_id', 'id');
    }

    // Join with peak_fare table
    public function peak_fare()
    {
        return $this->hasMany('App\Models\PeakFareDetail', 'fare_id', 'id')->orderByDesc('day');
    }
}
