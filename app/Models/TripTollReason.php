<?php

/**
 * Language Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    TollReason
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class TripTollReason extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    public $timestamps = false;

    protected static $logAttributes = [
        'trip_id', 'reason'
    ];
    protected static $logOnlyDirty = true;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['trip_id', 'reason'];
}
