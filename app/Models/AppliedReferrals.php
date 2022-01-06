<?php

/**
 * Applied Referrals Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Applied Referrals
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class AppliedReferrals extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion, LogsActivity;


    public $timestamps = true;

    public $convert_fields = ['amount'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'amount', 'currency_code'];

    protected static $logAttributes = [
        'user_id', 'amount', 'currency_code'
    ];
    protected static $logOnlyDirty = true;
}
