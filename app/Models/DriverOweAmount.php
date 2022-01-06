<?php

/**
 * Driver Owe Amount Model
 *
 * @package    GoferEats
 * @subpackage Model
 * @category   Driver Owe Amount
 * @author     Trioangle Product Team
 * @version    2.1
 * @link       http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class DriverOweAmount extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion, LogsActivity;

    protected $fillable = ['user_id', 'amount', 'currency_code'];

    protected $convert_fields = ['amount'];

    public $timestamps = false;


    protected static $logAttributes = [
        'user_id', ' amount', 'currency_code'
    ];
    protected static $logOnlyDirty = true;

    // Join with user table
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    // Join with Currency table
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
    }
}
