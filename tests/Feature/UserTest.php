<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_user(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'last_name' => 'biden',
            'email' => 'e@gmail.com',
            'phone' => '581-123-1234'
        ];

        $response = $this->postJson('/api/users',$json);

        $response->assertJsonFragment($json);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', $json);
    }

    public function test_post_user_should_return_422_if_first_name_is_empty(){
        $this->seed();

        $json = [
            'last_name' => 'biden',
            'email' => 'e@gmail.com',
            'phone' => '581-123-1234'
        ];

        $response = $this->postJson('/api/users',$json);

        $response->assertStatus(422);
    }


    public function test_post_user_should_return_422_if_last_name_is_empty(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'email' => 'e@gmail.com',
            'phone' => '581-123-1234'
        ];

        $response = $this->postJson('/api/users',$json);

        $response->assertStatus(422);
    }

    public function test_post_user_should_return_422_if_email_is_empty(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'last_name' => 'biden',
            'phone' => '581-123-1234'
        ];

        $response = $this->postJson('/api/users',$json);

        $response->assertStatus(422);
    }

    public function test_post_user_should_return_422_if_phone_is_empty(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'last_name' => 'biden',
            'email' => 'e@gmail.com'
        ];

        $response = $this->postJson('/api/users',$json);

        $response->assertStatus(422);
    }

    public function test_put_user(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'last_name' => 'biden',
            'email' => 'e@gmail.com',
            'phone' => '581-123-1234'
        ];

        $userRemoved = User::find(1)->toArray();

        $response = $this->putJson('/api/users/1',$json);

        $response->assertJsonFragment($json);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', $json);
        $this->assertDatabaseMissing('users', $userRemoved);
    }

    public function test_put_user_should_return_422_if_first_name_is_empty(){
        $this->seed();

        $json = [
            'last_name' => 'biden',
            'email' => 'e@gmail.com',
            'phone' => '581-123-1234'
        ];

        $response = $this->putJson('/api/users/1',$json);

        $response->assertStatus(422);
    }


    public function test_put_user_should_return_422_if_last_name_is_empty(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'email' => 'e@gmail.com',
            'phone' => '581-123-1234'
        ];

        $response = $this->putJson('/api/users/1',$json);

        $response->assertStatus(422);
    }

    public function test_put_user_should_return_422_if_email_is_empty(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'last_name' => 'biden',
            'phone' => '581-123-1234'
        ];

        $response = $this->putJson('/api/users/1',$json);

        $response->assertStatus(422);
    }

    public function test_put_user_should_return_422_if_phone_is_empty(){
        $this->seed();

        $json = [
            'first_name' => 'joe',
            'last_name' => 'biden',
            'email' => 'e@gmail.com'
        ];

        $response = $this->putJson('/api/users/1',$json);

        $response->assertStatus(422);
    }
}
