<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    // Opcional: Si quieres registrar la intención de donación antes de abrir Wompi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'donor_name' => 'nullable|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:500',
            'gateway' => 'required|string',
        ]);

        $reference = 'DESERET-' . strtoupper(Str::random(10));

        $donation = Donation::create([
            'reference' => $reference,
            'gateway' => $validated['gateway'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'donor_name' => $validated['donor_name'] ?: 'Anónimo',
            'donor_email' => $validated['donor_email'],
            'message' => $validated['message'],
        ]);

        return response()->json([
            'success' => true,
            'reference' => $reference,
            'donation_id' => $donation->id,
        ]);
    }
}
