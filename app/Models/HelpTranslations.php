<?php

/**
 * Help Translations Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Translations
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class HelpTranslations extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    public $timestamps = false;
    protected $fillable = ['name', 'description'];

    protected static $logAttributes = [
        'help_id', ' name', 'description', 'locale'
    ];
    protected static $logOnlyDirty = true;

    public function language()
    {
        return $this->belongsTo('App\Models\Language', 'locale', 'value');
    }
}
