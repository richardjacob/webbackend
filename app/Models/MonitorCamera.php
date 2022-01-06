<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;
class MonitorCamera extends Model
{
    // use \OwenIt\Auditing\Auditable;
    // use Notifiable, LogsActivity;
    // use LaravelEntrustUserTrait;

    protected $table = 'monitor_cameras';
    
     protected $fillable = [
        'driver_name','driver_id','vehicle_id','monitor_sim','monitor_imei','monitor_ip','monitor_status','camera_sim','camera_imei','camera_ip','camera_status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'driver_id', 'id');
    }

    // public function vehicle()
    // {
    //     return $this->belongsTo('App\Models\Vehicle', 'vehicle_id', 'id');
    // }

}
