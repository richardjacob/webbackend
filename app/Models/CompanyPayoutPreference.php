<?php

/**
 * Company Payout Preference Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Company Payout Preference
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeZone;
use DateTime;
use Config;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;


class  CompanyPayoutPreference extends Model implements Auditable
{

	use \OwenIt\Auditing\Auditable;
	use  LogsActivity;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'company_payout_preference';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['company_id', 'payout_method'];

	public $appends = ['updated_time', 'updated_date', 'account_holder_name'];


	protected static $logAttributes = [
		'company_id', 'address1', 'address2', 'city', 'state', 'postal_code', 'country', 'payout_method', 'paypal_email', 'currency_code', 'routing_number',
		'account_number', 'holder_name', 'holder_type', 'document_id', 'document_image', 'additional_document_id', 'additional_document_image', 'phone_number',
		'address_kanji', 'bank_name', 'bank_location', 'branch_name', 'branch_code', 'ssn_last_4'
	];
	protected static $logOnlyDirty = true;



	// Join with users table
	public function users()
	{
		return $this->belongsTo('App\Models\Company', 'company_id', 'id');
	}

	// Get Updated time for Payout Information
	public function getUpdatedTimeAttribute()
	{
		if (request()->segment(1) == 'api') {
			$date = PHP_DATE_FORMAT;
			$datemonth = date($date, strtotime($this->attributes['updated_at']));
			return $datemonth . ' at ' . date('H:i', strtotime($date));
		}

		$new_str = new DateTime($this->attributes['updated_at'], new DateTimeZone(Config::get('app.timezone')));

		$new_str->setTimeZone(new DateTimeZone('Asia/Kolkata'));

		$datemonth = date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at']));
		return $datemonth;
	}

	// Get Updated date for Payout Information
	public function getUpdatedDateAttribute()
	{
		if (request()->segment(1) == 'api') {
			$date = PHP_DATE_FORMAT;
			$datemonth = date($date, strtotime($this->attributes['updated_at']));
			return $datemonth;
		}

		$new_str = new DateTime($this->attributes['updated_at'], new DateTimeZone(Config::get('app.timezone')));

		$new_str->setTimeZone(new DateTimeZone('Asia/Kolkata'));

		$datemonth = date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at']));
		return $datemonth;
	}

	/**
	 * Get Document attribute
	 *
	 */
	public function getDocumentAttribute()
	{
		$image_name = $this->attributes['document_image'];
		$company_id = $this->attributes['company_id'];
		$url = url('images/companies/' . $company_id . '/payout_documents/' . $image_name);
		return $url;
	}

	//account_holder_name
	public function getAccountHolderNameAttribute()
	{
		return  $this->attributes['holder_name'];
	}
}
