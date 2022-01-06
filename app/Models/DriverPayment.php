<?php

/**
 * Payment Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Driver Payment
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class DriverPayment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion, LogsActivity;

    public $convert_fields = ['paid_amount'];
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'driver_payment';

    protected $guarded = [];

    public $timestamps = false;

    protected static $logAttributes = [
        'driver_id', ' last_trip_id', 'currency_code', 'paid_amount'
    ];
    protected static $logOnlyDirty = true;
}
