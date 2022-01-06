<?php

/**
 * ApiCredential Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    ApiCredential
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class ApiCredentials extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'api_credentials';

    public $timestamps = false;


    protected static $logAttributes = [
        'name', 'value', 'site'
    ];
    protected static $logOnlyDirty = true;
}
