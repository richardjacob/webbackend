<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use JWTAuth;

trait CurrencyConversion
{
	public $currency_code_field = 'currency_code';

	public $convert_currency_code;

	public $is_convert = false;

	public function __construct()
	{
		if (request()->segment(1) == 'admin') {
			// $currency_code = Cache()->remember('currency_code', Config('cache.one_year'), function(){
            //         return Currency::defaultCurrency()->first()->code;
            //     });

			//$currency_code = Currency::defaultCurrency()->first()->code;
			//$this->convert_currency_code =  $currency_code;
			$this->convert_currency_code =  'BDT';
		} else {
			$this->convert_currency_code = $this->getSessionOrDefaultCode();
		}
	}

	/**
	 * Get Session or Default Currency code
	 *
	 */
	public function getSessionOrDefaultCode()
	{
		// if (request()->segment(1) == 'api') {
		// 	if (request('token')) {
		// 		try {
		// 			// if (!Session::has('currency_code')){

		// 			// }

		// 			// $currency_code = Cache()->remember('currency_code', Config('cache.one_day'), function(){
		// 			// 	return JWTAuth::toUser(request()->token)->currency_code;
		// 			// });

		// 			$currency_code = JWTAuth::toUser(request()->token)->currency_code;

		// 		} catch (\Exception $e) {
		// 			$currency_code = Cache()->remember('currency_code', Config('cache.one_year'), function(){
		// 				return Currency::defaultCurrency()->first()->code;
		// 			});
		// 			//$currency_code = Currency::defaultCurrency()->first()->code;
		// 		}
		// 	} else {
		// 		$currency_code = Cache()->remember('currency_code', Config('cache.one_year'), function(){
		// 				return Currency::defaultCurrency()->first()->code;
		// 			});
		// 		//$currency_code = Currency::defaultCurrency()->first()->code;
		// 	}
		// 	if (!$currency_code) {
		// 		$currency_code = $this->get_currency_from_ip(request()->ip_address);
		// 	}
		// } else {
		// 	$currency_code = session()->get('currency');
		// }

		// $currency_code = $this->CheckCurrency($currency_code);

		// if (!$currency_code || $this->isAdminPanel()) {
		// 	$currency_code = Cache()->remember('currency_code', Config('cache.one_year'), function(){
		// 				return Currency::defaultCurrency()->first()->code;
		// 			});

		// 	//$currency_code = Currency::defaultCurrency()->first()->code;
		// }
		$currency_code = 'BDT';
		return $currency_code;
	}

	/**
	 * Check given currency code is active or not
	 *
	 */
	public function CheckCurrency($currency_code)
	{
		// Cache::get('key', function () {
		//     return DB::table(...)->get();
		// });

		// $currency = Cache()->remember('currency_code', Config('cache.one_year'), function() use ($currency_code){
		// 	return Currency:: where('code', $currency_code)->where('status', "Active")->first();
		// });

		// if (!$currency) {
		// 	$currency_code = Cache()->remember('currency_code', Config('cache.one_year'), function(){
		// 			return Currency::defaultCurrency()->first()->code;
		// 		});
		// }
		$currency_code = 'BDT';

		return $currency_code;
	}

	/**
	 * Get currency code to convert
	 *
	 */
	public function getConvertCurrencyCode()
	{
		return $this->convert_currency_code;
	}

	/**
	 * Set currency code to convert
	 *
	 */
	public function setConvertCurrencyCode($currency_code = '')
	{
		if ($currency_code == '') {
			$currency_code = $this->getSessionOrDefaultCode();
		}
		$this->convert_currency_code = $currency_code;
		return $this;
	}

	/**
	 * Disable admin panel coversion
	 *
	 */
	public function disableAdminPanelConversion()
	{
		return @$this->disable_admin_panel_convertion;
	}

	/**
	 * check request is from admin panel
	 *
	 */
	public function isAdminPanel()
	{
		return request()->segment(1) == 'admin';
	}

	/**
	 * check given model is convert or not
	 *
	 */
	public function getIsConvert()
	{
		return $this->is_convert;
	}

	/**
	 * set is convert to false to get original value
	 *
	 */
	public function original()
	{
		$this->is_convert = false;
		return $this;
	}

	/**
	 * set is convert to true to get converted value
	 *
	 */
	public function session()
	{
		$this->is_convert = false;
		return $this;
	}

	/**
	 * Get currency code
	 *
	 */
	public function getCurrencyCodeField()
	{
		return $this->currency_code_field;
	}

	/**
	 * Set currency code
	 *
	 */
	public function setCurrencyCodeField($currency_code_field)
	{
		$this->currency_code_field = $currency_code_field;
		return $this;
	}

	/**
	 * Check given attribute is convertable or not
	 *
	 */
	public function isConvertableAttribute($attribute)
	{
		return in_array($attribute, $this->getConvertFileds());
	}

	/**
	 * Get all convertable attributes
	 *
	 */
	public function getConvertFileds()
	{
		return $this->convert_fields ?: array();
	}

	/**
	 * @return array
	 */
	public function attributesToArray()
	{
		$attributes = parent::attributesToArray();
		if ($this->canConvert()) {
			foreach ($this->convert_fields as $field) {
				$attributes[$field] = $this->getAttribute($field);
			}
			$attributes['currency_code'] = $this->getToCurrencyCode();
		}

		return $attributes;
	}

	/**
	 * add currency code to appends array
	 *
	 */
	protected function getArrayableAppends()
	{
		$this->appends = array_unique(array_merge($this->appends, ['currency_symbol', 'original_currency_code']));

		return parent::getArrayableAppends();
	}

	/**
	 * check can convert or not
	 *
	 */
	public function canConvert()
	{
		return ($this->getIsConvert() && (!$this->isAdminPanel() || !$this->disableAdminPanelConversion()));
	}

	/**
	 * Override getattribue method to get coverted value
	 *
	 */
	public function getAttribute($attribute)
	{
		if ($this->canConvert()) {
			if ($this->isConvertableAttribute($attribute)) {
				$value = parent::getAttribute($attribute);
				$converted_value = $this->getConvertedValue($value);
				return $converted_value;
			}

			if ($attribute == 'currency_code') {
				return $this->getToCurrencyCode();
			}
		}

		return parent::getAttribute($attribute);
	}

	/**
	 * get Session currency
	 *
	 */
	public function getSessionCurrencyAttribute()
	{
		return Currency::whereCode($this->getSessionOrDefaultCode())->first();
	}

	/**
	 * get Original currency symbol
	 *
	 */
	public function getCurrencySymbolAttribute()
	{
		if ($this->getSessionCurrencyAttribute()) {
			return $this->getSessionCurrencyAttribute()->symbol;
		}
		return '$';
	}

	/**
	 * get Original currency code
	 *
	 */
	public function getOriginalCurrencyCodeAttribute()
	{
		return $this->getOriginal('currency_code');
	}

	/**
	 * get Current currency code
	 *
	 */
	public function getFromCurrencyCode()
	{
		$field = $this->getCurrencyCodeField();
		return parent::getAttribute($field) ?: '';
	}

	/**
	 * get Target Currency code to convert
	 *
	 */
	public function getToCurrencyCode()
	{
		$code = $this->getConvertCurrencyCode();
		return $code;
	}

	/**
	 * get Converted Value
	 *
	 */
	public function getConvertedValue($price)
	{
		$from = $this->getFromCurrencyCode();
		$to = $this->getToCurrencyCode();
		$converted_price = $this->currency_convert($from, $to, $price);
		return $converted_price;
	}

	/**
	 * Currency Convert
	 *
	 * @param int $from   Currency Code From
	 * @param int $to     Currency Code To
	 * @param int $price  Price Amount
	 * @return int Converted amount
	 */
	public function currency_convert($from = '', $to = '', $price = 0)
	{
		$usd_amount = floatval($price);
		$session_rate = 1;
		// if ($from == '') {
		// 	$from = $this->getSessionOrDefaultCode();
		// }
		// if ($to == '') {
		// 	$to = $this->getSessionOrDefaultCode();
		// }

		// // $rate = Currency::whereCode($from)->first()->rate;
		// $rate = Cache()->remember('rate', Config('cache.one_year'), function()use ($from) {
		// 	return Currency::whereCode($from)->first()->rate;
		// });



		// // $session_rate = Currency::whereCode($to)->first();
		// $session_rate = Cache()->remember('session_rate', Config('cache.one_year'), function()use ($to) {
		// 	return Currency::whereCode($to)->first();
		// });

		// if ($session_rate) {
		// 	$session_rate = $session_rate->rate;
		// } else {
		// 	$session_rate = '1';
		// }

		// if ($rate != "0.0") {
		// 	if ($price) {
		// 		$usd_amount = floatval($price) / floatval($rate);
		// 	} else {
		// 		$usd_amount = 0;
		// 	}
		// } else {
		// 	echo "Error Message : Currency value '0' (" . $from . ')';
		// 	die;
		// }

		return number_format($usd_amount * $session_rate, 2, '.', '');
	}

	/**
	 * Get Currency code from IP address
	 * @param $ip_address 
	 * @return $currency_code
	 */
	public function get_currency_from_ip($ip_address = '')
	{
		$ip_address = $ip_address ?: request()->getClientIp();
		$default_currency = Currency::active()->defaultCurrency()->first();
		$currency_code    = @$default_currency->code;
		if (session()->get('currency_code')) {
			$currency_code = session()->get('currency_code');
		} else if ($ip_address != '') {
			$result = array();
			try {
				$result = unserialize(file_get_contents_curl('http://www.geoplugin.net/php.gp?ip=' . $ip_address));
			} catch (\Exception $e) {
			}
			// Default Currency code for footer
			if (@$result['geoplugin_currencyCode']) {
				$currency_code =  $result['geoplugin_currencyCode'];
			}
			session()->put('currency_code', $currency_code);
		}
		return $currency_code;
	}
}
