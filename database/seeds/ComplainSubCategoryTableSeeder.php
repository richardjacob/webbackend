<?php

use Illuminate\Database\Seeder;

class ComplainSubCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('complain_sub_categories')->delete();
        DB::table('complain_sub_categories')->insert([
            ['id'=>'1', 'complain_cat_id' =>'1', 'sub_category' =>'Inability to update vital portions of a rider profile on the app.', 'sub_category_bn' => 'অ্যাপে রাইডার প্রোফাইলের গুরুত্বপূর্ণ অংশ আপডেট করতে অক্ষমতা।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'2', 'complain_cat_id' =>'1', 'sub_category' =>'Unprofessional and rude drivers.', 'sub_category_bn' => 'অ-পেশাদার এবং অভদ্র ড্রাইভার।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'3', 'complain_cat_id' =>'1', 'sub_category' =>'Dangerous drivers.', 'sub_category_bn' => 'বিপজ্জনক ড্রাইভার।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'4', 'complain_cat_id' =>'1', 'sub_category' =>'Inability to book a trip.', 'sub_category_bn' => 'ট্রিপ বুক করতে অক্ষম।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'5', 'complain_cat_id' =>'1', 'sub_category' =>'Driver not showing up with cancellation fee.', 'sub_category_bn' => 'বাতিলকরণ ফি সহ চালক উপস্থিত হচ্ছেন না।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'6', 'complain_cat_id' =>'1', 'sub_category' =>'Hacked account resulting in charges.', 'sub_category_bn' => 'চার্জের ফলে অ্যাকাউন্ট হ্যাকড হয়েছে।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'7', 'complain_cat_id' =>'1', 'sub_category' =>'Dropped off at incorrect locations.', 'sub_category_bn' => 'ভুল জায়গায় ড্রপ করা হয়েছে।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'8', 'complain_cat_id' =>'1', 'sub_category' =>'Allowing minors to create account and book rides without an adult’s consent.', 'sub_category_bn' => 'প্রাপ্তবয়স্কদের সম্মতি ছাড়াই নাবালকদের অ্যাকাউন্ট তৈরি করতে এবং রাইড বুক করার অনুমতি দেওয়া।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],

            ['id'=>'9', 'complain_cat_id' =>'2', 'sub_category' =>'Credit card declined and locked when card worked for other services.', 'sub_category_bn' => 'যখন কার্ড অন্যান্য পরিষেবার জন্য কাজ করে তখন ক্রেডিট কার্ড প্রত্যাখ্যান এবং লক হয়ে যায়।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'10', 'complain_cat_id' =>'2', 'sub_category' =>'Account charged multiple times for the same ride.', 'sub_category_bn' => 'একই রাইডের জন্য একাধিকবার অ্যাকাউন্ট চার্জ করা হয়েছে।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'11', 'complain_cat_id' =>'2', 'sub_category' =>'Mysterious fees taken out and not refunded.', 'sub_category_bn' => 'রহস্যজনক ফি নেওয়া হয়েছে এবং ফেরত দেওয়া হয়নি।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'12', 'complain_cat_id' =>'2', 'sub_category' =>'No information for riders on surge pricing.', 'sub_category_bn' => 'বর্ধিত মূল্যের উপর রাইডারদের জন্য কোন তথ্য নেই।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'13', 'complain_cat_id' =>'2', 'sub_category' =>'Issuing incorrect cleaning fees.', 'sub_category_bn' => 'বর্ধিত মূল্যের উপর রাইডারদের জন্য কোনো তথ্য নেই।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'14', 'complain_cat_id' =>'2', 'sub_category' =>'Toll fees mistakenly charged to rider’s account.', 'sub_category_bn' => 'টোল ফি ভুলবশত রাইডারদের অ্যাকাউন্টে চার্জ করা হয়েছে।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],

            ['id'=>'15', 'complain_cat_id' =>'3', 'sub_category' =>'Inability to redeem voucher.', 'sub_category_bn' => 'ভাউচার ভাঙ্গাতে অক্ষমতা।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'16', 'complain_cat_id' =>'3', 'sub_category' =>'Inability to use advertised discount on rides.', 'sub_category_bn' => 'রাইডগুলিতে বিজ্ঞাপিত ছাড় ব্যবহার করতে অক্ষমতা।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'17', 'complain_cat_id' =>'3', 'sub_category' =>'False advertisement on estimated fare for ride.', 'sub_category_bn' => 'রাইড এর আনুমানিক ভাড়া ভূল প্রদর্শন।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            ['id'=>'18', 'complain_cat_id' =>'3', 'sub_category' =>'Not honoring bonus payment for new drivers.', 'sub_category_bn' => 'নতুন চালকদের জন্য বোনাস প্রদানকে সম্মান না করা।', 'status'=>'1', 'created_at' =>'2021-12-19 11:45:00', 'updated_at' => "2021-12-19 11:45:00"],
            
        ]);
    }
}
