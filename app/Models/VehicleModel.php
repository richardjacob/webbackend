<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class VehicleModel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    //
    public $timestamps = false;

    public $table = 'vehicle_model';

    protected static $logAttributes = [
        'vehicle_make_id', 'model_name', 'status'
    ];
    protected static $logOnlyDirty = true;




    public function vehicle_make()
    {
        return $this->belongsTo('App\Models\MakeVehicle', 'vehicle_make_id', 'id');
    }

    /**
     * Scope to get Active records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
