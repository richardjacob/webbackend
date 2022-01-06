<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class FilterOptionTranslations extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    protected $table = 'filter_options_translations';

    protected static $logAttributes = [
        'filter_option_id', 'name', 'locale'
    ];
    protected static $logOnlyDirty = true;

    public function language()
    {
        return $this->belongsTo('App\Models\Language', 'locale', 'value');
    }
}
