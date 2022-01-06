<?php

/**
 * Cancel Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Cancel
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Cancel extends Model  implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cancel';

    protected $fillable = ['user_id', 'trip_id', 'cancel_reason_id', 'cancel_comments', 'cancelled_by'];

    protected static $logAttributes = [
        'trip_id', 'user_id', 'cancel_reason_id', 'cancel_comments', 'cancelled_by'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Join With Trip Table
     *
     */
    public function trip()
    {
        return $this->hasOne('App\Models\Trips', 'id', 'trip_id');
    }

    /**
     * Join With Cancel Reson Table
     *
     */
    public function cancel_reason()
    {
        return $this->hasOne('App\Models\CancelReason', 'id', 'cancel_reason_id');
    }
}
