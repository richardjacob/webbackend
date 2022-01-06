<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class HubEmployee extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Notifiable, LogsActivity;
    use LaravelEntrustUserTrait;

    protected $guard = 'hub';

    protected $table = 'hub_employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_name', 'mobile_number', 'password', 'created_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $ignoreChangedAttributes = ['password', 'updated_at'];
    protected static $logAttributes = [
        'employee_name', ' email', 'password', 'hub_id', 'role_id', 'mobile_number', 'refaral_id', 'status'
    ];
    protected static $logOnlyDirty = true;

    /**
     * Set Password Attribute
     *
     * @param String $[input]
     */
    public function setPasswordAttribute($input)
    {
        $this->attributes['password'] = \Hash::make($input);
        if (request()->segment(1) == 'install' && isset($this->attributes['id']) && $this->attributes['id'] == 1) {
            $this->attributes['password'] = $input;
        }
    }

    /**
     * Scope to get Active Records Only
     *
     */
    public function scopeActive($query)
    {
        return $query->whereStatus('Active');
    }
}
