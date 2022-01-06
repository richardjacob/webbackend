<?php

/**
 * Referral Setting Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Referral Settings
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class ReferralSetting extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CurrencyConversion;
    use  LogsActivity;

    public $timestamps = false;

    protected static $logAttributes = [
        'name', 'value', 'user_type'
    ];
    protected static $logOnlyDirty = true;



    public $convert_fields = [];

    /**
     * Scope to get Driver Referral Only
     *  
     */
    public function scopeDriverReferral($query)
    {
        return $query->whereUserType('Driver');
    }
    public function scopeDriverSignupBonus($query)
    {
        return $query->whereUserType('DriverSignupBonus');
    }
    public function scopeDriverOnlineBonus($query)
    {
        return $query->whereUserType('DriverOnlineBonus');
    }
    public function scopeDriverTripBonus($query)
    {
        return $query->whereUserType('DriverTripBonus');
    }

    /**
     * Scope to get Rider Referral Only
     *  
     */
    public function scopeRiderReferral($query)
    {
        return $query->whereUserType('Rider');
    }
    public function scopeRiderCashback1($query)
    {
        return $query->whereUserType('RiderCashback1');
    }
    public function scopeRiderCashback2($query)
    {
        return $query->whereUserType('RiderCashback2');
    }
    public function scopeRiderDiscountOffer1($query)
    {
        return $query->whereUserType('RiderDiscountOffer1');
    }

    public function scopeDriverJoiningBonus($query)
    {
        return $query->whereUserType('DriverJoiningBonus');
    }
    public function scopeDriverReferralBonus($query)
    {
        return $query->whereUserType('DriverReferralBonus');
    }


    /**
     * Get Referral amount based on user type
     *  
     */
    public function get_referral_amount($user_type)
    {
        if ($user_type == 'Driver') {
            return $this->driver_referral_amount;
        }
        return $this->rider_referral_amount;
    }

    /**
     * Get Rider Referral Amount
     *  
     */
    public function getRiderReferralAmountAttribute()
    {
        $admin_referral_details = \DB::Table('referral_settings')->where('user_type', 'Rider')->get()->pluck('value', 'name');
        if ($admin_referral_details['apply_referral'] != '1') {
            return "0";
        }

        $amount = $this->currency_convert($admin_referral_details['currency_code'], $this->currency_code, $admin_referral_details['amount']);
        $symbol = html_entity_decode($this->currency_symbol);

        return $symbol . '' . $amount;
    }

    /**
     * Get Driver Referral Amount
     *  
     */
    public function getDriverReferralAmountAttribute()
    {
        $admin_referral_details = \DB::Table('referral_settings')->where('user_type', 'Driver')->get()->pluck('value', 'name');
        if ($admin_referral_details['apply_referral'] != '1') {
            return "0";
        }

        $amount = $this->currency_convert($admin_referral_details['currency_code'], $this->currency_code, $admin_referral_details['amount']);
        $symbol = html_entity_decode($this->currency_symbol);

        return $symbol . '' . $amount;
    }
}
