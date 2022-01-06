<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class PayoutTransactionHistory extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use LogsActivity;

    protected $table = 'payout_transaction_history';

    protected static $logAttributes = [
        'driver_id',
    ];
    protected static $logOnlyDirty = true;
}
