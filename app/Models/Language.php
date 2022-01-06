<?php

/**
 * Language Model
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

class Language extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'language';

    public $timestamps = false;

    protected static $logAttributes = [
        'name', ' value', 'status', 'default_language'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Scope to get Active records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
