<?php

/**
 * Car Type Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Car Type
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class CarType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'car_type';

    protected static $logAttributes = [
        'car_name', 'description', 'vehicle_image', 'active_image', 'is_pool', 'status'
    ];
    protected static $logOnlyDirty = true;

    public $timestamps = false;

    /**
     * get Vehicle Image Attribute
     *
     */
    public function getVehicleImageAttribute()
    {
        return url('images/car_image/' . $this->attributes['vehicle_image']);
    }

    /**
     * get Vehicle Active Image Attribute
     *
     */
    public function getActiveImageAttribute()
    {
        $url = \App::runningInConsole() ? SITE_URL : url('/');
        return $url . '/images/car_image/' . $this->attributes['active_image'];
    }

    /**
     * Scope to get Active Records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->whereStatus('Active');
    }

    /**
     * Join with fare table
     *
     */
    public function manage_fare()
    {
        return $this->belongsTo('App\Models\ManageFare', 'id', 'vehicle_id');
    }
}
