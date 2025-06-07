<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_weather_endpoint()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/weather', [
            'city' => 'Caracas',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'weather',
                'saved',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_weather_endpoint()
    {
        $response = $this->postJson('/api/weather', [
            'city' => 'Caracas',
        ]);

        $response->assertStatus(401);
    }
}
