<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    /**
     * Testa o login com credenciais válidas.
     *
     * @return void
     */
    public function testLoginWithValidCredentials()
    {
        $data = [
            'email' => 'example@gmail.com',
            'password' => 'password',
        ];

        $user = User::create([
            'name' => 'teste',
            'email' => 'example@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/api/login', $data);
        $response->assertStatus(200);
    }

    /**
     * Testa o login com credenciais inválidas.
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials()
    {
        $data = [
            'email' => 'example@gmail.com',
            'password' => 'invalid_password',
        ];

        $response = $this->post('/api/login', $data);
        $response->assertStatus(401);
    }

    public function testRegister()
    {
       
        $name = $this->faker->name;
        $email = $this->faker->unique()->safeEmail;
        $password = $this->faker->password;

        $response = $this->post('/api/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Register successful',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function testLogout()
    {
        $user = User::factory()->create();

        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);

        $this->assertGuest();
    }

    public function testRefresh()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'authorisation' => [
                    'token',
                    'type',
                ],
            ]);

        $responseJson = $response->json();
        $this->assertArrayHasKey('token', $responseJson['authorisation']);
        $this->assertAuthenticatedAs($user);
    }
}
