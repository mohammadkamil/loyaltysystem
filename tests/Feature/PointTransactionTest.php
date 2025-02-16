<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\PointTransaction;
use function Pest\Laravel\{actingAs, getJson, postJson, deleteJson};

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['business_id' => $this->user->id]);
    $this->transaction = PointTransaction::factory()->create(['customer_id' => $this->customer->id]);
});

test('user must be authenticated to access point transactions', function () {
    getJson('/api/transactions')->assertUnauthorized();
});

test('can list point transactions for authenticated user', function () {
    actingAs($this->user)
        ->getJson('/api/transactions')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'customer_id', 'points', 'type', 'created_at']
            ]
        ]);
});

test('can create a new point transaction', function () {
    $transactionData = [
        'customer_id' => $this->customer->id,
        'points' => 50,
        'type' => 'add',
    ];

    actingAs($this->user)
        ->postJson('/api/transactions', $transactionData)
        ->assertCreated()
        ->assertJsonFragment(['points' => 50, 'type' => 'add']);
});

test('prevents transactions with invalid type', function () {
    $transactionData = [
        'customer_id' => $this->customer->id,
        'points' => 30,
        'type' => 'invalid_type',
    ];

    actingAs($this->user)
        ->postJson('/api/transactions', $transactionData)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('can retrieve a single point transaction', function () {
    actingAs($this->user)
        ->getJson("/api/transactions/{$this->transaction->id}")
        ->assertOk()
        ->assertJsonFragment(['id' => $this->transaction->id]);
});

test('can delete a point transaction', function () {
    actingAs($this->user)
        ->deleteJson("/api/transactions/{$this->transaction->id}")
        ->assertOk()
        ->assertJson(['message' => 'Transaction deleted successfully']);

    expect(PointTransaction::find($this->transaction->id))->toBeNull();
});
