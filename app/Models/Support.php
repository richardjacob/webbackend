<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class Support extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = ['name', 'status', 'cancelled_by'];

    public $timestamps = false;

    protected static $logAttributes = [
        'name', 'link', 'status', 'image'
    ];
    protected static $logOnlyDirty = true;




    public function getImageSrcAttribute()
    {
        return url('images/support/' . $this->attributes['image']);
    }

    public function scopeActive($query)
    {
        return $query->whereStatus('Active');
    }
}
