<?php

/**
 * PaymentMethod Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    PaymentMethod
 * @author      Trioangle Product Team
 * @version     1.7
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentMethod extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_method';


    public $timestamps = false;
    protected static $logAttributes = [
        'user_id', ' customer_id', 'intent_id', 'payment_method_id', 'brand', 'last4'
    ];
    protected static $logOnlyDirty = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
