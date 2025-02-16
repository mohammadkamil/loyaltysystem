<?php

use App\Models\Customer;
use App\Models\User;
use function Pest\Laravel\{actingAs, getJson, postJson, putJson, deleteJson};

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['business_id' => $this->user->id]);
});

// Authentication test
test('user must be authenticated to access customers', function () {
    getJson('/api/customers')->assertUnauthorized();
});

// Listing customers
test('can list customers for authenticated user', function () {
    Customer::factory()->count(3)->create(['business_id' => $this->user->id]); // Ensure data exists

    actingAs($this->user)
        ->getJson('/api/customers')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [  // Ensure pagination format
                '*' => ['id', 'name', 'email', 'phone', 'total_points', 'business_id']
            ]
        ]);
});


// Creating a new customer
test('can create a new customer', function () {
    $customerData = Customer::factory()->make()->toArray();
    $customerData['business_id'] = $this->user->id;

    actingAs($this->user)
        ->postJson('/api/customers', $customerData)
        ->assertCreated()
        ->assertJsonFragment(['name' => $customerData['name']]);
});

// Prevent duplicate emails
test('prevents duplicate customer emails', function () {
    $customerData = Customer::factory()->make(['email' => $this->customer->email])->toArray();

    actingAs($this->user)
        ->postJson('/api/customers', $customerData)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

// Retrieving a single customer
test('can retrieve a single customer', function () {
    actingAs($this->user)
        ->getJson("/api/customers/{$this->customer->id}")
        ->assertOk()
        ->assertJsonFragment(['name' => $this->customer->name]);
});

// Restrict access to other users' customers
test('cannot retrieve another user\'s customer', function () {
    $otherUser = User::factory()->create();
    $otherCustomer = Customer::factory()->create(['business_id' => $otherUser->id]);

    actingAs($this->user)
        ->getJson("/api/customers/{$otherCustomer->id}")
        ->assertForbidden();
});

// Updating a customer
test('can update a customer', function () {
    $newData = ['name' => 'Updated Name'];

    actingAs($this->user)
        ->putJson("/api/customers/{$this->customer->id}", $newData)
        ->assertOk()
        ->assertJsonFragment($newData);
});

// Restrict updating another user's customer
test('cannot update another user\'s customer', function () {
    $otherUser = User::factory()->create();
    $otherCustomer = Customer::factory()->create(['business_id' => $otherUser->id]);

    actingAs($this->user)
        ->putJson("/api/customers/{$otherCustomer->id}", ['name' => 'Hacked Name'])
        ->assertForbidden();
});

// Deleting a customer
test('can delete a customer', function () {
    actingAs($this->user)
        ->deleteJson("/api/customers/{$this->customer->id}")
        ->assertOk()
        ->assertJson(['message' => 'Customer deleted']);

    expect(Customer::find($this->customer->id))->toBeNull();
});

// Restrict deleting another user's customer
test('cannot delete another user\'s customer', function () {
    $otherUser = User::factory()->create();
    $otherCustomer = Customer::factory()->create(['business_id' => $otherUser->id]);

    actingAs($this->user)
        ->deleteJson("/api/customers/{$otherCustomer->id}")
        ->assertForbidden();
});
