<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\Sport;
use App\Models\User;
use App\Models\Review;
use App\Models\Rental;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_get_equipments(){
        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $sport1 = Sport::create(['name' => 'Vélo']);
        $sport2 = Sport::create(['name' => 'Randonnée']);

        $equipment1 = Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment2 = Equipment::create([
            'name' => 'Vélo de bouette',
            'description' => 'Vélo sous-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment1->sports()->attach($sport1->id);
        $equipment1->sports()->attach($sport2->id);

        $equipment2->sports()->attach($sport1->id);
        $equipment2->sports()->attach($sport2->id);

        $response = $this->get('/api/equipments');

        $equipment_array = $response->decodeResponseJson();

        $this->assertEquals(count($equipment_array['data']), 2);
        $response->assertJsonFragment([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category' => [
                'name' => 'Véhicule',
            ],
            'sports' => [
                ['name' => 'Vélo',],
                ['name' => 'Randonnée',],
            ]
        ]);
        $response->assertStatus(OK);
    }

    public function test_get_equipments_by_id(){
        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $sport1 = Sport::create(['name' => 'Vélo']);
        $sport2 = Sport::create(['name' => 'Randonnée']);

        $equipment1 = Equipment::create([
            'equipment_id' => '12',
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment2 = Equipment::create([
            'name' => 'Vélo de bouette',
            'description' => 'Vélo sous-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment1->sports()->attach($sport1->id);
        $equipment1->sports()->attach($sport2->id);

        $equipment2->sports()->attach($sport1->id);
        $equipment2->sports()->attach($sport2->id);

        $response = $this->get('/api/equipments/' . $equipment1->id);

        $equipment_array = $response->decodeResponseJson();

        $response->assertJsonIsObject();
        $response->assertJsonFragment([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category' => [
                'name' => 'Véhicule',
            ],
            'sports' => [
                ['name' => 'Vélo',],
                ['name' => 'Randonnée',],
            ]
        ]);
        $response->assertStatus(OK);
    }

    public function test_get_equipments_by_id_should_return_404_when_id_isnt_in_DB(){

        $response = $this->get('/api/equipments/1');

        $response->assertStatus(NOT_FOUND);
    }

    public function test_get_popularity_index_when_equipment_has_rentals_and_reviews(){
        User::factory(10)->create();

        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $sport1 = Sport::create(['name' => 'Vélo']);

        $equipment1 = Equipment::create([
            'equipment_id' => '12',
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment1->sports()->attach($sport1->id);

        Rental::factory(10)->create(['equipment_id' => $equipment1->id]);

        Review::factory(10)->create(['rating' => 10]);
        Review::factory(10)->create(['rating' => 5]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/popularity_indexes');

        $equipment_array = $response->decodeResponseJson();

        $response->assertJsonIsObject();

        $expectedResult = 9;
        $response->assertJsonFragment(['popularityIndex' => $expectedResult]);

        $response->assertStatus(OK);
    }

    public function test_popularity_index_is_0_when_equipment_has_rentals_but_no_reviews(){
        User::factory(10)->create();

        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $sport1 = Sport::create(['name' => 'Vélo']);

        $equipment1 = Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment1->sports()->attach($sport1->id);

        Rental::factory(10)->create(['equipment_id' => $equipment1->id]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/popularity_indexes');

        $response->assertJsonIsObject();

        $expectedResult = 0;
        $response->assertJsonFragment(['popularityIndex' => $expectedResult]);

        $response->assertStatus(OK);
    }


    public function test_popularity_index_is_0_when_equipment_has_no_rentals_and_no_reviews(){
        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $sport1 = Sport::create(['name' => 'Vélo']);

        $equipment1 = Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $equipment1->sports()->attach($sport1->id);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/popularity_indexes');

        $response->assertJsonIsObject();

        $expectedResult = 0;
        $response->assertJsonFragment(['popularityIndex' => $expectedResult]);

        $response->assertStatus(OK);
    }


    public function test_popularity_index_should_return_404_when_id_isnt_in_DB(){
        $response = $this->get('/api/equipments/1/popularity_indexes');

        $response->assertStatus(NOT_FOUND);
    }

    public function test_get_average_rental_price_works_with_dates_inclusively(){
        User::factory(10)->create();

        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        for($i = 0; $i < 4; $i++){
            Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        }
        $equipment1 = Equipment::create([
            'name' => 'Vélo de bouette',
            'description' => 'Vélo sous-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-10',
            'total_price' => 0,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-10',
            'total_price' => 300,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-10',
            'total_price' => 300,
        ]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/average_rental_price?minDate=2024-01-01&maxDate=2024-03-10');

        $response->assertJsonIsObject();

        $expectedResult = 200;
        $response->assertJsonFragment(['averageRentalPrice' => $expectedResult]);

        $response->assertStatus(OK);
    }

    public function test_get_average_rental_price_works_with_only_min_date(){
        User::factory(10)->create();

        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        for($i = 0; $i < 4; $i++){
            Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        }
        $equipment1 = Equipment::create([
            'name' => 'Vélo de bouette',
            'description' => 'Vélo sous-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-10',
            'total_price' => 100,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-10',
            'total_price' => 200,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-10',
            'total_price' => 300,
        ]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/average_rental_price?minDate=2024-02-01');

        $response->assertJsonIsObject();

        $expectedResult = 250;
        $response->assertJsonFragment(['averageRentalPrice' => $expectedResult]);

        $response->assertStatus(OK);
    }

    public function test_get_average_rental_price_works_with_only_max_date(){
        User::factory(10)->create();

        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        for($i = 0; $i < 4; $i++){
            Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        }
        $equipment1 = Equipment::create([
            'name' => 'Vélo de bouette',
            'description' => 'Vélo sous-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-10',
            'total_price' => 100,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-10',
            'total_price' => 200,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-10',
            'total_price' => 300,
        ]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/average_rental_price?maxDate=2024-02-10');

        $response->assertJsonIsObject();

        $expectedResult = 150;
        $response->assertJsonFragment(['averageRentalPrice' => $expectedResult]);

        $response->assertStatus(OK);
    }

    public function test_get_average_rental_price_works_with_no_dates(){
        User::factory(10)->create();

        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        for($i = 0; $i < 4; $i++){
            Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        }
        $equipment1 = Equipment::create([
            'name' => 'Vélo de bouette',
            'description' => 'Vélo sous-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-10',
            'total_price' => 100,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-10',
            'total_price' => 200,
        ]);

        Rental::factory()->create([
            'equipment_id' => $equipment1->id,
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-10',
            'total_price' => 300,
        ]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/average_rental_price');

        $response->assertJsonIsObject();

        $expectedResult = 200;
        $response->assertJsonFragment(['averageRentalPrice' => $expectedResult]);

        $response->assertStatus(OK);
    }

    public function test_get_average_should_abort_with_code_422_if_min_date_is_greater_then_max_date(){
        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $equipment1 = Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/average_rental_price?minDate=2024-12-01&maxDate=2024-01-01');

        $response->assertStatus(INVALID_DATA);
    }

    public function test_get_average_should_abort_with_code_422_if_date_format_is_invalid(){
        $category = Category::create([
            'name' => 'Véhicule',
        ]);

        $equipment1 = Equipment::create([
            'name' => 'Vélo de montagne',
            'description' => 'Vélo tout-terrain',
            'daily_price' => 35,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/api/equipments/' . $equipment1->id . '/average_rental_price?minDate=2023/01/01&maxDate=2024-01-01');

        $response->assertStatus(INVALID_DATA);
    }
}
