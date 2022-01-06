<?php

/**
 * Payment Gateway Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Payment Gateway
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentGateway extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_gateway';

    public $timestamps = false;

    protected static $logAttributes = [
        'name', 'value', 'site'
    ];
    protected static $logOnlyDirty = true;
}
