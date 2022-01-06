<?php

/**
 * Rider Location Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Rider Location
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class RiderLocation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rider_location';

    public $timestamps = false;

    protected $guarded = [];

    protected static $logAttributes = [
        'user_id', 'home', 'work', 'home_latitude', 'home_longitude', 'work_latitude', 'work_longitude', 'latitude', 'longitude'
    ];
    protected static $logOnlyDirty = true;
}
