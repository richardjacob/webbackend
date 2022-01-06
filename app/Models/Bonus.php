<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class Bonus extends Model  implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    //protected $table = 'bonuses';

    protected static $logAttributes = [
        'user_id', 'referred_by', 'referral_to', 'user_type', 'bonus_type', 'withdrawal_method', 'bonus_amount', 'number_of_trips', 'completed_trips',
        'number_of_days', 'currency_code', 'min_hour', 'min_trip', 'who_get_bonus', 'status', 'payment_status', 'terms_condition', 'unique_id',
        'online_bonus_date'
    ];
    protected static $logOnlyDirty = true;
}
