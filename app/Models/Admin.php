<?php

/**
 * Admin Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Admin
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;
use DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class Admin extends Authenticatable implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    use Notifiable, LogsActivity;
    use LaravelEntrustUserTrait;

    protected $guard = 'admin';

    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];



    protected static $ignoreChangedAttributes = ['password', 'updated_at'];

    protected static $logAttributes = [
        'username', 'email', 'password', 'country_code', 'mobile_number', 'status'
    ];
    protected static $logOnlyDirty = true;



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

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
