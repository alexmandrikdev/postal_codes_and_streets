<?php

namespace Database\Seeders;

use App\Models\PostalCode;
use App\Models\Street;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        PostalCode::factory(100)->create();
        Street::factory(100)->create();
    }
}
