<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\Sport;

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
}
