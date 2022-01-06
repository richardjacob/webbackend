<?php

use Illuminate\Database\Seeder;

class DBCleanTableTruncateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bonus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('profile_picture')->truncate();
        DB::table('users_promo_code')->truncate();
        DB::table('rider_location')->truncate();
        DB::table('schedule_cancel')->truncate();
        DB::table('schedule_ride')->truncate();
        DB::table('emergency_sos')->truncate();
        DB::table('request')->truncate();
        DB::table('cancel')->truncate();
        DB::table('rating')->truncate();
        DB::table('wallet')->truncate();
        DB::table('applied_referrals')->truncate();
        DB::table('referral_users')->truncate();
        DB::table('driver_payment')->truncate();
        DB::table('driver_address')->truncate();
        DB::table('driver_location')->truncate();
        DB::table('driver_documents')->truncate();
        DB::table('driver_owe_amounts')->truncate();
        DB::table('driver_owe_amount_payments')->truncate();
        DB::table('vehicle')->truncate();
        DB::table('payment_method')->truncate();
        DB::table('trip_toll_reasons')->truncate();
        DB::table('payment')->truncate();
        DB::table('trips')->truncate();
        DB::table('pool_trips')->truncate();
        DB::table('users')->truncate();
        DB::table('bonus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
