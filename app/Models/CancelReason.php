<?php

/**
 * Cancel Reson Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    CancelReason
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class CancelReason extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reason', 'status', 'cancelled_by'];

    protected static $logAttributes = [
        'reason', 'cancelled_by', 'status'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Scope to get Active Records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
