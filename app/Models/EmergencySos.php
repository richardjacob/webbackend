<?php

/**
 * Emercency Sos Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    EmergencySOS
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class EmergencySos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'emergency_sos';

    public $timestamps = false;

    protected $appends = ['original_number'];


    protected static $logAttributes = [
        'user_id', ' name', 'country_code', 'mobile_number', 'country_id'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Get Mobile Number Value
     *
     */
    public function getMobileNumberAttribute()
    {
        return $this->attributes['country_code'] . $this->attributes['mobile_number'];
    }

    /**
     * Get Original Number Value
     *
     */
    public function getOriginalNumberAttribute()
    {
        return $this->attributes['mobile_number'];
    }

    // Join with country table
    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    // Join with country table
    public function getCountryCodeAttribute()
    {
        return $this->country->short_name;
    }
}
