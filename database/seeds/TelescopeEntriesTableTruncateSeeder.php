<?php

use Illuminate\Database\Seeder;

class TelescopeEntriesTableTruncateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('telescope_monitoring')->truncate();
        DB::table('telescope_entries_tags')->truncate();
        DB::table('telescope_entries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
