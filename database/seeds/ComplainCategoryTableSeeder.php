<?php

use Illuminate\Database\Seeder;

class ComplainCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('complain_categories')->delete();
        DB::table('complain_categories')->insert([
            ['id'=>'1', 'category' =>'Problems with a service', 'category_bn' => 'পরিষেবা নিয়ে সমস্যা', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'2', 'category' =>'Billing and collections', 'category_bn' => 'বিল এবং সংগ্রহ', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'3', 'category' =>'Advertising and sales', 'category_bn' => 'বিজ্ঞাপন এবং বিক্রয়', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
        ]);
    }
}
