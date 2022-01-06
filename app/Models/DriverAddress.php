<?php

/**
 * Driver Address Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Driver Address
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class DriverAddress extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'driver_address';

    public $timestamps = false;

    protected static $logAttributes = [
        'user_id', ' address_line1', 'address_line2', 'city', 'state', 'postal_code'
    ];
    protected static $logOnlyDirty = true;
}
