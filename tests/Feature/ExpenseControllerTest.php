<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Expense;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function testStoreExpense()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $expenseData = [
            'description' => $this->faker->sentence,
            'reference_date' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 0, 1000),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/expenses', $expenseData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Expense registered successfully',
                'data' => $expenseData,
            ]);

    
        $this->assertDatabaseHas('expenses', $expenseData);
        $this->assertAuthenticatedAs($user);
    }

    public function testUpdateExpense()
    {
        
        $user = User::factory()->create();

        $expense = Expense::create([
            'user_id' => $user->id,
            'description' => $this->faker->sentence,
            'reference_date' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 0, 1000)
        ]);

        $token = auth()->login($user);

        $updatedData = [
            'description' => $this->faker->sentence,
            'reference_date' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 0, 1000),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/expenses/' . $expense->id, $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Expense updated successfully',
                'data' => $updatedData,
            ]);

        $this->assertDatabaseHas('expenses', $updatedData);

        $this->assertAuthenticatedAs($user);
    }

    public function testShowExpense()
    {
        $user = User::factory()->create();

        $expense = Expense::create([
            'user_id' => $user->id,
            'description' => $this->faker->sentence,
            'reference_date' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 0, 1000)
        ]);

        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/expenses/' . $expense->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => $expense->toArray(),
            ]);

        $this->assertAuthenticatedAs($user);
    }

    public function testDestroyExpense()
    {
        $user = User::factory()->create();

        $expense = Expense::create([
            'user_id' => $user->id,
            'description' => $this->faker->sentence,
            'reference_date' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 0, 1000)
        ]);

        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/expenses/' . $expense->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Expense deleted successfully',
            ]);

        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }
}
