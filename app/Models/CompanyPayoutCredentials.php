<?php

/**
 * Company Payout Credentials Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Company Payout Credentials
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyPayoutCredentials extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'type'];


    protected static $logAttributes = [
        'company_id', 'preference_id', 'default', 'type', 'payout_id'
    ];
    protected static $logOnlyDirty = true;

    // Return the companies default payout_preference details
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    // Return the company default payout_preference details
    public function company_payout_preference()
    {
        return $this->belongsTo('App\Models\CompanyPayoutPreference', 'preference_id', 'id');
    }
}