<?php

use Illuminate\Database\Seeder;

class FareTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('manage_fare')->delete();

        DB::table('manage_fare')->insert(array(
            ['location_id' => '1', 'vehicle_id' => '1', 'base_fare' => '50', 'capacity' => '2', 'min_fare' => '80', 'per_min' => '5', 'per_km' => '2', 'schedule_fare' => '100', 'currency_code' => 'USD', 'apply_peak' => 'No', 'apply_night' => 'No'],
            ['location_id' => '1', 'vehicle_id' => '2', 'base_fare' => '30', 'capacity' => '3', 'min_fare' => '60', 'per_min' => '4', 'per_km' => '1', 'schedule_fare' => '200', 'currency_code' => 'USD', 'apply_peak' => 'No', 'apply_night' => 'No'],
            ['location_id' => '1', 'vehicle_id' => '4', 'base_fare' => '10', 'capacity' => '4', 'min_fare' => '40', 'per_min' => '2', 'per_km' => '1', 'schedule_fare' => '100', 'currency_code' => 'USD', 'apply_peak' => 'No', 'apply_night' => 'No'],
        ));
    }   
}
