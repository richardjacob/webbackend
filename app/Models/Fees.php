<?php

/**
 * Fees Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Fees
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Fees extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fees';

    public $timestamps = false;

    protected static $logAttributes = [
        'name', ' value'
    ];
    protected static $logOnlyDirty = true;
}
