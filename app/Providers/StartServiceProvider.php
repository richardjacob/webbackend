<?php

/**
 * StartService Provider
 *
 * @package     Gofer
 * @subpackage  Provider
 * @category    Service
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;
use Config;
use Schema;
use Auth;
use App;
use App\Models\Pages;
use App\Models\Language;
use App\Models\Currency;
use App\Models\SiteSettings;
use App\Models\JoinUs;
use App\Models\User;
use App\Models\ReferralSetting;
use App\Models\ReferralUser;
use App\Models\Trips;
use App\Models\Country;
use App\Observers\TripObserver;
use App\Observers\UserObserver;

class StartServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }


    public function boot()
    {
        if (env('DB_DATABASE') != '') {
            $has_pages = Cache()->remember('has_pages', Config('cache.one_year'), function(){
                    return Schema::hasTable('pages');
                });
            if ($has_pages)
            //if (Schema::hasTable('pages'))
                $this->pages(); // Calling Pages function

            $has_site_settings = Cache()->remember('has_site_settings', Config('cache.one_year'), function(){
                    return Schema::hasTable('site_settings');
                });
            if ($has_site_settings)
            //if (Schema::hasTable('site_settings'))
                $this->site_settings(); // Calling Site Settings function

            $has_join_us = Cache()->remember('has_join_us', Config('cache.one_year'), function(){
                    return Schema::hasTable('join_us');
                });
            if ($has_join_us)
            //if (Schema::hasTable('join_us'))
                $this->join_us(); // Calling Join US function

            $has_currency = Cache()->remember('has_currency', Config('cache.one_year'), function(){
                    return Schema::hasTable('currency');
                });
            if ($has_currency)
            //if (Schema::hasTable('currency'))
                $this->currency(); // Calling Join US function

            $has_language = Cache()->remember('has_language', Config('cache.one_year'), function(){
                    return Schema::hasTable('language');
                });
            if ($has_language)
            //if (Schema::hasTable('language'))
                $this->language(); // Calling Join US function

            $has_users = Cache()->remember('has_users', Config('cache.one_year'), function(){
                    return Schema::hasTable('users');
                });
            if ($has_users){
            //if (Schema::hasTable('users')) {
                $this->registerReferralEvent(); // Calling register Referral Event function
            }

            $has_trips = Cache()->remember('has_trips', Config('cache.one_year'), function(){
                    return Schema::hasTable('trips');
                });
            if ($has_trips) {
            //if (Schema::hasTable('trips')) {
                // Cache()->remember('trip_observe', Config('cache.one_year'), function(){
                //     return Trips::observe(TripObserver::class);
                // });

                Trips::observe(TripObserver::class);
            }

            $has_users = Cache()->remember('has_users', Config('cache.one_year'), function(){
                    return Schema::hasTable('users');
                });
            if ($has_users) {
            //if (Schema::hasTable('users')) {
                // Cache()->remember('user_observe', Config('cache.one_year'), function(){
                //     return User::observe(UserObserver::class);
                // });

                User::observe(UserObserver::class);
            }
        }
    }

    // Share Static Pages data to whole software
    public function pages()
    {
        // Pages lists for footer
        $company_pages = Cache()->remember('company_pages', Config('cache.one_year'), function(){
                    return Pages::select('url', 'name')->where('status', '=', 'Active')->where('footer', 'yes')->get();
                });

        //$company_pages = Pages::select('url', 'name')->where('status', '=', 'Active')->where('footer', 'yes')->get();

        View::share('company_pages', $company_pages);
    }

    // Share Join Us data to whole software
    public function join_us()
    {
        $join_us = Cache()->remember('join_us', Config('cache.one_year'), function(){
                return JoinUs::whereNotIn('name', ['app_store_rider', 'app_store_driver', 'play_store_rider', 'play_store_driver'])->get();
            });
        //$join_us = JoinUs::whereNotIn('name', ['app_store_rider', 'app_store_driver', 'play_store_rider', 'play_store_driver'])->get();

        $app_links = Cache()->remember('app_links', Config('cache.one_year'), function(){
                return JoinUs::whereIn('name', ['app_store_rider', 'app_store_driver', 'play_store_rider', 'play_store_driver'])->get();
            });
        //$app_links = JoinUs::whereIn('name', ['app_store_rider', 'app_store_driver', 'play_store_rider', 'play_store_driver'])->get();

        View::share('join_us', $join_us);
        View::share('app_links', $app_links);
    }

    public function currency()
    {
        // Currency code lists for footer

         $currency = Cache()->remember('currency', Config('cache.one_year'), function(){
                return Currency::codeSelect();
            });
        //$currency = Currency::codeSelect();
        View::share('currency_select', $currency);

        $default_currency = Cache()->remember('default_currency', Config('cache.one_year'), function(){
                    return Currency::active()->defaultCurrency()->first();
                });
        //$default_currency = Currency::active()->defaultCurrency()->first();

        if (!@$default_currency)
            $default_currency = Cache()->remember('default_currency', Config('cache.one_year'), function(){
                    return Currency::active()->first();
                });
            //$default_currency = Currency::active()->first();

        session(['currency' => $default_currency->code]);
        session(['symbol' => $default_currency->symbol]);
        View::share('default_currency', $default_currency);
        View::share('default_country', 'India');
    }

    public function language()
    {
        // Language lists for footer
        $language = Cache()->remember('language', Config('cache.one_year'), function(){
                return Language::active()->pluck('name', 'value');;
            });
        //$language = Language::active()->pluck('name', 'value');

        View::share('language', $language);
        $country = Cache()->remember('country', Config('cache.one_year'), function(){
                return Country::get();
            });

        //$country = Country::get();

        View::share('country_lists', $country->pluck('long_name', 'phone_code'));
        // Default Language for footer

        $default_language = Cache()->remember('default_language', Config('cache.one_year'), function(){
                return Language::where('default_language', '=', '1')->limit(1)->get();
            });

        //$default_language = Language::where('default_language', '=', '1')->limit(1)->get();
        View::share('default_language', $default_language);
        if ($default_language->count() > 0) {
            session(['language' => $default_language[0]->value]);
            App::setLocale($default_language[0]->value);
        }
    }


    // Share Site Settings data to whole software
    public function site_settings()
    {
        $site_settings = resolve('site_settings');

        View::share('site_name', $site_settings[0]->value);
        View::share('head_code', $site_settings[7]->value);
        View::share('version', $site_settings->where('name', 'version')->first()->value);
        View::share('version', \Str::random(4));

        if ($site_settings[10]->value == '' && @$_SERVER['HTTP_HOST'] && !\App::runningInConsole()) {

            $url = "http://" . $_SERVER['HTTP_HOST'];
            $url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

            SiteSettings::where('name', 'site_url')->update(['value' =>  $url]);
        }
    }

    public function registerReferralEvent()
    {
        User::created(function ($user) {
            $referral_code = $user->getUniqueReferralCode();
            $user->setAttribute('referral_code', $referral_code);
            $user->save();

            $admin_referral_details = \DB::Table('referral_settings')->where('user_type', $user->user_type)->get()->pluck('value', 'name');

            if ($admin_referral_details['apply_referral']) {
                $referred_user = User::where('referral_code', $user->used_referral_code)->first();
                if ($referred_user != '') {
                    $referrel_user = new ReferralUser;
                    $referrel_user->referral_id = $user->id;
                    $referrel_user->user_id     = $referred_user->id;
                    $referrel_user->user_type   = $referred_user->user_type;
                    $referrel_user->save();
                }
            }
        });

        ReferralUser::creating(function ($referrel_user) {

            $user_type = $referrel_user->user_type;

            $admin_referral_details = \DB::Table('referral_settings')->where('user_type', $user_type)->get()->pluck('value', 'name');

            if ($admin_referral_details['apply_referral']) {
                $c_date = date('Y-m-d');
                $referrel_user->setAttribute('days', $admin_referral_details['number_of_days']);
                $referrel_user->setAttribute('trips', $admin_referral_details['number_of_trips']);
                $referrel_user->setAttribute('start_date', $c_date);
                $referrel_user->setAttribute('end_date', date('Y-m-d', strtotime($c_date . ' +' . $admin_referral_details['number_of_days'] . ' days')));
                $referrel_user->setAttribute('currency_code', $admin_referral_details['currency_code']);
                $referrel_user->setAttribute('amount', $admin_referral_details['amount']); //referral_amount
                $referrel_user->setAttribute('pending_amount', $admin_referral_details['amount']); //referral_amount
                $bonus_helper = resolve('App\Http\Helper\BonusHelper');
                //if ($user_type == 'Driver') $bonus_helper->driver_referral_bonus($referrel_user);
                //else if ($user_type == 'Rider') $bonus_helper->rider_referral_bonus($referrel_user);
            }
        });
    }
}
