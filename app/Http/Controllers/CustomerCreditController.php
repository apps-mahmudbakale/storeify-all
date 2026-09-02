<?php

namespace App\Http\Controllers;

use App\Models\CustomerCredit;
use Illuminate\Http\Request;

class CustomerCreditController extends Controller
{
    // Show all customer credits
    public function index()
    {
        $credits = CustomerCredit::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        
        $pending = CustomerCredit::pending()->sum('amount');
        $cleared = CustomerCredit::fullyCleared()->sum('amount');

        return view('customer-credits.index', compact('credits', 'pending', 'cleared'));
    }

    // Show form to add new credit
    public function create()
    {
        return view('customer-credits.create');
    }

    // Store new credit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'invoice' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        CustomerCredit::create($validated);

        return redirect()->route('app.customer-credits.index')
            ->with('success', 'Customer credit recorded successfully');
    }

    // Show credit details
    public function show(CustomerCredit $customerCredit)
    {
        return view('customer-credits.show', compact('customerCredit'));
    }

    // Show edit form
    public function edit(CustomerCredit $customerCredit)
    {
        return view('customer-credits.edit', compact('customerCredit'));
    }

    // Update credit
    public function update(Request $request, CustomerCredit $customerCredit)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'invoice' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'amount_cleared' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        // Update status based on amount_cleared
        if ($validated['amount_cleared'] <= 0) {
            $validated['status'] = 'pending';
        } elseif ($validated['amount_cleared'] >= $validated['amount']) {
            $validated['status'] = 'fully_cleared';
        } else {
            $validated['status'] = 'partially_cleared';
        }

        $customerCredit->update($validated);

        return redirect()->route('app.customer-credits.show', $customerCredit)
            ->with('success', 'Customer credit updated successfully');
    }

    // Delete credit
    public function destroy(CustomerCredit $customerCredit)
    {
        $customerCredit->delete();

        return redirect()->route('app.customer-credits.index')
            ->with('success', 'Customer credit deleted successfully');
    }

    // Mark as fully cleared
    public function markCleared(CustomerCredit $customerCredit)
    {
        $customerCredit->update([
            'amount_cleared' => $customerCredit->amount,
            'status' => 'fully_cleared'
        ]);

        return redirect()->route('app.customer-credits.show', $customerCredit)
            ->with('success', 'Credit marked as fully cleared');
    }
}

