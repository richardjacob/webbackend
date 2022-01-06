<?php

/**
 * Help Sub Category Lang Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Sub Category Lang
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class HelpSubCategoryLang extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'help_sub_category_lang';

    protected $fillable = ['name', 'description'];

    public $timestamps = false;

    protected static $logAttributes = [
        'sub_category_id', 'name', 'description', 'locale'
    ];
    protected static $logOnlyDirty = true;



    public function language()
    {
        return $this->belongsTo('App\Models\Language', 'locale', 'value');
    }
}
