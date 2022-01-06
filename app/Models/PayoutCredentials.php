<?php

/**
 * Payout Credentials Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Payout Credentials
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class PayoutCredentials extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'type'];


    protected static $logAttributes = [
        'user_id', ' preference_id', 'default', 'type', 'payout_id'
    ];
    protected static $logOnlyDirty = true;

    // Return the drivers default payout_preference details
    public function payout_preference()
    {
        return $this->belongsTo('App\Models\PayoutPreference', 'preference_id', 'id');
    }

    // Join with users table
    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
