<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
// use Database\Seeders\CategorySeeder;
// use Database\Seeders\EquipmentSeeder;
// use Database\Seeders\EquipmentSportSeeder;
// use Database\Seeders\SportSeeder;
use App\Models\Rental;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Log::info('this is databaseSeeder');

         $this->call([
            CategorySeeder::class,
            SportSeeder::class,
            EquipmentSeeder::class,
            EquipmentSportSeeder::class,
        ]);

        User::factory(10)->create();
        Rental::factory(10)->create();
        Review::factory(10)->create();
    }
}
