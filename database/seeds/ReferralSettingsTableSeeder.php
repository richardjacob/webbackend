<?php

use Illuminate\Database\Seeder;

class ReferralSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('referral_settings')->delete();

        DB::table('referral_settings')->insert([
            ['name' => 'apply_referral',    'value' => '1', 'user_type' => 'Driver'],
            ['name' => 'number_of_trips',   'value' => '5',  'user_type' => 'Driver'],
            ['name' => 'number_of_days',    'value' => '3',  'user_type' => 'Driver'],
            ['name' => 'currency_code',     'value' => 'BDT',  'user_type' => 'Driver'],
            ['name' => 'referral_amount',   'value' => '20',  'user_type' => 'Driver'],

            ['name' => 'apply_referral',    'value' => '1', 'user_type' => 'Rider'],
            ['name' => 'number_of_trips',   'value' => '5',  'user_type' => 'Rider'],
            ['name' => 'number_of_days',    'value' => '3',  'user_type' => 'Rider'],
            ['name' => 'currency_code',     'value' => 'BDT',  'user_type' => 'Rider'],
            ['name' => 'referral_amount',   'value' => '20',  'user_type' => 'Rider'],

            ['name' => 'apply_referral',    'value' => '0', 'user_type' => 'DriverSignupBonus'],
            ['name' => 'currency_code',     'value' => 'BDT',  'user_type' => 'DriverSignupBonus'],
            ['name' => 'bonus_amount',   'value' => '0',  'user_type' => 'DriverSignupBonus'],
            ['name' => 'withdrawal_method',   'value' => 'Cash',  'user_type' => 'DriverSignupBonus'],

            ['name' => 'apply_referral',    'value' => '0', 'user_type' => 'DriverOnlineBonus'],
            ['name' => 'min_hour',     'value' => '8',  'user_type' => 'DriverOnlineBonus'],
            ['name' => 'currency_code',     'value' => 'BDT',  'user_type' => 'DriverOnlineBonus'],
            ['name' => 'bonus_amount',   'value' => '0',  'user_type' => 'DriverOnlineBonus'],
            ['name' => 'withdrawal_method',   'value' => 'Wallet',  'user_type' => 'DriverOnlineBonus'],

            ['name' => 'apply_referral',    'value' => '0', 'user_type' => 'DriverTripBonus'],
            ['name' => 'currency_code',     'value' => 'BDT',  'user_type' => 'DriverTripBonus'],
            ['name' => 'bonus_amount1',   'value' => '0',  'user_type' => 'DriverTripBonus'],
            ['name' => 'bonus_amount2',   'value' => '0',  'user_type' => 'DriverTripBonus'],
            ['name' => 'bonus_amount3',   'value' => '0',  'user_type' => 'DriverTripBonus'],
            ['name' => 'bonus_amount4',   'value' => '0',  'user_type' => 'DriverTripBonus'],
            ['name' => 'bonus_amount5',   'value' => '0',  'user_type' => 'DriverTripBonus'],
            ['name' => 'withdrawal_method',   'value' => 'Wallet',  'user_type' => 'DriverTripBonus'],
        ]);
    }
}