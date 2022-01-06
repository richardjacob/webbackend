<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Wallet extends Model  implements Auditable
{
    use CurrencyConversion;
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected $table = 'wallet';

    protected $fillable = ['user_id', 'amount', 'paykey', 'currency_code'];
    protected $appends = ['original_amount'];

    public $timestamps = false;

    protected $convert_fields = ['amount', 'original_amount'];

    public $disable_admin_panel_convertion = true;

    protected $primaryKey = 'user_id';


    protected static $logAttributes = [
        'user_type', 'user_id', 'amount', 'currency_code', 'paykey'
    ];
    protected static $logOnlyDirty = true;



    /**
     * Get Amount
     *
     */
    public function getAmountAttribute()
    {
        return number_format(($this->attributes['amount']), 2, '.', '0');
    }

    /**
     * Get Original Amount
     *
     */
    public function getOriginalAmountAttribute()
    {
        return $this->attributes['amount'];
    }
}
