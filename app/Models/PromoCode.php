<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class PromoCode extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion, LogsActivity;

    protected $table = 'promo_code';

    protected $appends = ['expire_date_dmy', 'expire_date_mdy', 'original_amount'];

    protected $convert_fields = ['amount', 'original_amount'];
    public $disable_admin_panel_convertion = true;


    protected static $logAttributes = [
        'code', 'amount', 'currency_code', 'expire_date', 'status'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Get Expire Date in Dmy Format
     *  
     */
    public function getExpireDateDmyAttribute()
    {
        return date('d-m-Y', strtotime($this->attributes['expire_date']));
    }

    /**
     * Get Expire Date in Mdy Format
     *  
     */
    public function getExpireDateMdyAttribute()
    {
        return date('m/d/Y', strtotime($this->attributes['expire_date']));
    }

    /**
     * Get Amount Attribute
     *  
     */
    public function getAmountAttribute()
    {
        return number_format(($this->attributes['amount']), 2, '.', '');
    }

    /**
     * Get Original Amount Attribute
     *  
     */
    public function getOriginalAmountAttribute()
    {
        return $this->attributes['amount'];
    }
}
