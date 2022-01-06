<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['comment', 'status', 'comment_by'];

    protected static $logAttributes = [
        'comment', 'comment_by', 'status'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Scope to get Active Records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
