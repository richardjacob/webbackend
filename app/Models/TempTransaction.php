<?php

/**
 * ApiCredential Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    ApiCredential
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class TempTransaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'temp_transactions';

    public $timestamps = false;

    protected static $logAttributes = [
        'payment_type', 'order_id', 'user_id', 'user_type', 'trip_id', 'payeer_number', 'amount', 'applied_referral_amount', 'mb_tr_id', 'payment_gateway_name',
        'payment_gateway_number', 'status', 'transaction_time'
    ];
    protected static $logOnlyDirty = true;
}
