<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $storageSub = $user->getOrCreateStorageSub();
        $computeSub = $user->getOrCreateComputeSub();
        
        // Dummy data for billing history
        $invoices = [
            ['id' => 'INV-202605-01', 'date' => now()->subDays(5), 'amount' => $storageSub->price + $computeSub->price, 'status' => 'Paid', 'items' => ['Storage ' . ucfirst($storageSub->plan), 'Compute ' . ucfirst($computeSub->plan)]],
            ['id' => 'INV-202604-01', 'date' => now()->subDays(35), 'amount' => $storageSub->price + $computeSub->price, 'status' => 'Paid', 'items' => ['Storage ' . ucfirst($storageSub->plan), 'Compute ' . ucfirst($computeSub->plan)]],
        ];
        
        return view('billing.index', compact('user', 'storageSub', 'computeSub', 'invoices'));
    }
}
