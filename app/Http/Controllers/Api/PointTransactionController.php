<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Point Transactions
 *
 * APIs for managing customer point transactions.
 */
class PointTransactionController extends Controller
{
    /**
     * Get all point transactions
     * 
     * Retrieves a list of all point transactions owned by the authenticated user.
     * 
     * @authenticated
     * 
     * @response 200 {
     *  "data": [
     *    {
     *      "id": 1,
     *      "customer_id": 3,
     *      "customer_name": "John Doe",
     *      "points": 10,
     *      "type": "add",
     *      "created_at": "2024-02-15 10:00:00"
     *    }
     *  ]
     * }
     */
    public function index()
    {
        $transactions = PointTransaction::ownedBy(Auth::id())
            ->with('customer:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($transactions);
    }

    /**
     * Store a new point transaction
     *
     * Creates a transaction and updates the customer's total points.
     * 
     * @authenticated
     *
     * @bodyParam customer_id integer required The ID of the customer receiving points. Example: 3
     * @bodyParam points integer required The number of points to add or deduct. Example: 20
     * @bodyParam type string required Must be 'add' or 'reduce'. Example: "add"
     *
     * @response 201 {
     *  "message": "Transaction recorded successfully",
     *  "transaction": {
     *    "id": 5,
     *    "customer_id": 3,
     *    "points": 20,
     *    "type": "add",
     *    "created_at": "2024-02-15 10:30:00"
     *  }
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'required|integer|min:1',
            'type' => 'required|in:add,reduce',
        ]);

        $customer = Customer::where('id', $request->customer_id)
            ->where('business_id', Auth::id())
            ->firstOrFail();

        $transaction = PointTransaction::create([
            'customer_id' => $customer->id,
            'points' => $request->points,
            'type' => $request->type,
        ]);

        return response()->json([
            'message' => 'Transaction recorded successfully',
            'transaction' => $transaction,
        ], 201);
    }

    /**
     * Get a specific point transaction
     * 
     * Retrieve details of a specific transaction.
     * 
     * @authenticated
     * 
     * @urlParam id integer required The ID of the transaction. Example: 1
     * 
     * @response 200 {
     *  "id": 1,
     *  "customer_id": 3,
     *  "points": 10,
     *  "type": "add",
     *  "created_at": "2024-02-15 10:00:00"
     * }
     */
    public function show($id)
    {
        $transaction = PointTransaction::ownedBy(Auth::id())->findOrFail($id);

        return response()->json($transaction);
    }

    /**
     * Delete a point transaction
     * 
     * Remove a specific transaction (admin only).
     * 
     * @authenticated
     * 
     * @urlParam id integer required The ID of the transaction. Example: 5
     * 
     * @response 200 {
     *  "message": "Transaction deleted successfully"
     * }
     */
    public function destroy($id)
    {
        $transaction = PointTransaction::ownedBy(Auth::id())->findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
