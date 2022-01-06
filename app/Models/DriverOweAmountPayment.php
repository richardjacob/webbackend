<?php

/**
 * DriverOweAmountPayment Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    DriverOweAmountPayment
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class DriverOweAmountPayment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion, LogsActivity;

    protected $fillable = ['user_id', 'transaction_id', 'amount', 'currency_code', 'status'];

    protected $convert_fields = ['amount'];

    public $timestamps = false;


    protected static $logAttributes = [
        'user_id', ' transaction_id', 'amount', 'currency_code', 'status'
    ];
    protected static $logOnlyDirty = true;



    /**
     * get formatted Amount Value
     *
     */
    public function getAmountAttribute()
    {
        return number_format(($this->attributes['amount']), 2);
    }

    /**
     * Join With User table
     *
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
