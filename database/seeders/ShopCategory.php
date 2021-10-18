<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class ShopCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shop_categories')->insert([
            'name' => 'Cafe',
            'created_at' => '2021-10-02 10:43:56',
            'updated_at' => '2021-10-02 10:43:56'
        ]);
    }
}
