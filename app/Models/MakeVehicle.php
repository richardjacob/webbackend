<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class MakeVehicle extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    //
    public $timestamps = false;

    public $table = 'vehicle_make';


    protected static $logAttributes = [
        'make_vehicle_name', ' status'
    ];
    protected static $logOnlyDirty = true;


    public function getMakeNameAttribute($query)
    {
        return $this->attributes['make_vehicle_name'];
    }

    /**
     * Scope to get Active records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Join with model table
    public function vehicle_model()
    {
        return $this->hasMany('App\Models\VehicleModel', 'vehicle_make_id', 'id')->active();
    }

    public static function getMakeModel()
    {
        return MakeVehicle::with('vehicle_model')->active()->get();
    }
}
