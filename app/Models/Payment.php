<?php

/**
 * Payment Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Payment
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment';

    protected $fillable = ['trip_id', 'correlation_id', 'admin_transaction_id', 'driver_transaction_id', 'driver_payout_status', 'admin_payout_status', 'payout_amount', 'driver_id', 'payment_date'];

    public $timestamps = false;

    protected static $logAttributes = [
        'trip_id', 'correlation_id', 'admin_transaction_id', 'driver_transaction_id', 'admin_payout_status', 'driver_payout_status'
    ];
    protected static $logOnlyDirty = true;
}
