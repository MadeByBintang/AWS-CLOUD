<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\StorageSubscription;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $storageSub  = $user->getOrCreateStorageSub();
        $computeSub  = $user->getOrCreateComputeSub();

        // Ambil riwayat langganan storage sebagai invoice nyata dari database
        $storageHistory = $user->storageSubscriptions()
            ->orderByDesc('created_at')
            ->get();

        // Buat koleksi invoice dari riwayat subscription
        $invoices = $storageHistory->map(function ($sub, $index) {
            $invoiceNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $year  = $sub->created_at->format('Y');
            $month = $sub->created_at->format('m');
            return [
                'id'     => "INV-{$year}{$month}-{$invoiceNum}",
                'date'   => $sub->created_at,
                'amount' => $sub->price,
                'plan'   => \App\Models\StorageSubscription::planLabels()[$sub->plan] ?? ucfirst($sub->plan),
                'status' => $sub->price == 0 ? 'Free' : ($sub->is_active ? 'Paid' : 'Expired'),
                'is_active' => $sub->is_active,
            ];
        });

        // Total tagihan bulan ini (hanya langganan aktif berbayar)
        $monthlyTotal = ($storageSub->price ?? 0) + ($computeSub->price ?? 0);

        return view('billing.index', compact(
            'user', 'storageSub', 'computeSub', 'invoices', 'monthlyTotal'
        ));
    }
}
