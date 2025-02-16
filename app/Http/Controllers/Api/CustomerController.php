<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Customer Management
 *
 * APIs for managing customers
 */
class CustomerController extends Controller
{
    /**
     * Get all customers
     *
     * @authenticated
     *
     * @response 200 {
     *   "customers": [
     *     {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "phone": "+60123456789",
     *       "total_points": 50,
     *       "business_id": 1
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $customers = Customer::where('business_id', Auth::id())->paginate(10); // Ensure pagination
        return response()->json($customers);
    }
    

    /**
     * Create a new customer
     *
     * @authenticated
     *
     * @bodyParam name string required The customer's name. Example: John Doe
     * @bodyParam email string nullable The customer's email. Example: john@example.com
     * @bodyParam phone string nullable The customer's phone number. Example: +60123456789
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "John Doe",
     *   "email": "john@example.com",
     *   "phone": "+60123456789",
     *   "total_points": 0,
     *   "business_id": 1
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            ...$validated,
            'business_id' => Auth::id(),
            'total_points' => 0, // Default points
        ]);

        return response()->json($customer, 201);
    }

    /**
     * Get a single customer
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the customer. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "John Doe",
     *   "email": "john@example.com",
     *   "phone": "+60123456789",
     *   "total_points": 50,
     *   "business_id": 1
     * }
     */
    public function show(Customer $customer)
    {
        if ($customer->business_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($customer);
    }

    /**
     * Update a customer
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the customer. Example: 1
     * @bodyParam name string optional The customer's new name. Example: Jane Doe
     * @bodyParam email string optional The customer's new email. Example: jane@example.com
     * @bodyParam phone string optional The customer's new phone number. Example: +60123456789
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Jane Doe",
     *   "email": "jane@example.com",
     *   "phone": "+60123456789",
     *   "total_points": 50,
     *   "business_id": 1
     * }
     */
    public function update(Request $request, Customer $customer)
    {
        if ($customer->business_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:customers,email,' . $customer->id,
            'phone' => 'sometimes|string|max:20',
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    /**
     * Delete a customer
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the customer. Example: 1
     *
     * @response 200 {
     *   "message": "Customer deleted"
     * }
     */
    public function destroy(Customer $customer)
    {
        if ($customer->business_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer->delete();
        return response()->json(['message' => 'Customer deleted']);
    }
}
