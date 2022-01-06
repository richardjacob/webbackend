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


class TollReason extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;


    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reason', 'status'];

    protected static $logAttributes = [
        'reason', 'status'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Scope to get Active records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
