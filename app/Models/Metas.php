<?php

/**
 * Metas Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Metas
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Metas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'metas';

    public $timestamps = false;
    protected static $logAttributes = [
        'url', 'title', 'description', 'keywords'
    ];
    protected static $logOnlyDirty = true;
}
