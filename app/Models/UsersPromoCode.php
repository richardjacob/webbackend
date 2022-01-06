<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class UsersPromoCode extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected $table = 'users_promo_code';

    protected static $logAttributes = [
        'user_id', 'promo_code_id', 'trip_id'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Join with promo_code table
     *
     */
    public function promo_code()
    {
        return $this->belongsTo('App\Models\PromoCode', 'promo_code_id', 'id')->where('expire_date', '>=', date('Y-m-d'));
    }

    /**
     * Join with promo_code table
     *
     */
    public function promo_code_many()
    {
        return $this->hasMany('App\Models\PromoCode', 'id', 'promo_code_id')->where('expire_date', '>=', date('Y-m-d'))->limit(1);
    }
}
