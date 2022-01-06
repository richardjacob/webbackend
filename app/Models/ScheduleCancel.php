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

class ScheduleCancel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'schedule_cancel';

    protected $fillable = ['id', 'schedule_ride_id', 'cancel_reason', 'cancel_by', 'cancel_reason_id'];

    protected static $logAttributes = [
        'schedule_ride_id', 'cancel_reason', 'cancel_reason_id', 'cancel_by'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Join with cancel reasons attribute
     *  
     */
    public function cancel_reasons()
    {
        return $this->hasOne('App\Models\CancelReason', 'id', 'cancel_reason_id');
    }
}
