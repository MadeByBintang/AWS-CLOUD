<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Services\MiniStackService;
use App\Models\Credential;
use App\Models\ActivityLog;

class CredentialController extends Controller
{
    protected $miniStack;

    public function __construct(MiniStackService $miniStack)
    {
        $this->miniStack = $miniStack;
    }

    /**
     * Meminta pembuatan Access Key & Secret Key ke MiniStack
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return back()->with('error', 'Pilih paket langganan terlebih dahulu.');
        }

        // 1. Cek Kuota Kredensial
        $totalKeys = $user->credentials()->where('is_active', true)->count();
        if ($totalKeys >= $subscription->key_limit) {
            return back()->with('error', 'Batas maksimal Access Key sudah tercapai. Silakan upgrade paket.');
        }

        // 2. Minta kredensial ke MiniStack (Gunakan ID agar unik)
        $miniStackUser = 'user_' . $user->id;
        $keys = $this->miniStack->generateCredentials($miniStackUser);

        if (!$keys || !isset($keys['access_key']) || !isset($keys['secret_key'])) {
            return back()->with('error', 'Gagal men-generate kredensial dari MiniStack.');
        }

        // 3. Simpan ke database secara AMAN (Secret Key DIENKRIPSI)
        Credential::create([
            'user_id' => $user->id,
            'access_key' => $keys['access_key'],
            // Enkripsi bawaan Laravel agar aman di database
            'secret_key' => Crypt::encryptString($keys['secret_key']),
            'is_active' => true,
        ]);

        // 4. Catat Aktivitas
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Generate Credential',
            'resource_type' => 'Access Key',
            'resource_name' => $keys['access_key'],
            'ip_address' => $request->ip(),
            'metadata' => json_encode(['ministack_user' => $miniStackUser])
        ]);

        return redirect()->route('dashboard')->with('success', 'Access Key baru berhasil dibuat!');
    }

    /**
     * Menampilkan Secret Key secara aman (Hanya saat tombol Reveal diklik)
     */
    public function reveal(Credential $cred)
    {
        // Pastikan key ini milik user yang sedang login
        if ($cred->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // DEKRIPSI Secret Key untuk dikembalikan ke tampilan web
            $secretKey = Crypt::decryptString($cred->secret_key);

            // Catat log bahwa user melihat secret key
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Reveal Secret Key',
                'resource_type' => 'Access Key',
                'resource_name' => $cred->access_key,
                'ip_address' => request()->ip(),
                'metadata' => json_encode([])
            ]);

            return response()->json([
                'success' => true,
                'secret_key' => $secretKey
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendekripsi Secret Key.'
            ], 500);
        }
    }

    /**
     * Menampilkan daftar semua access key milik user.
     */
    public function index()
    {
        $user        = Auth::user();
        $credentials = $user->credentials()->latest()->get();
        $subscription = $user->subscription;
        $keyLimit    = $subscription?->key_limit ?? 3;

        return view('credentials.index', compact('credentials', 'keyLimit'));
    }

    /**
     * Tampilkan form buat key baru (opsional, karena create via POST langsung).
     */
    public function create()
    {
        return redirect()->route('credentials.index');
    }

    /**
     * Menonaktifkan / menghapus access key.
     */
    public function destroy(Credential $cred)
    {
        if ($cred->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $cred->update(['is_active' => false]);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Revoke Credential',
            'resource_type' => 'Access Key',
            'resource_name' => $cred->access_key,
            'ip_address'    => request()->ip(),
            'metadata'      => json_encode([])
        ]);

        return back()->with('success', 'Access Key berhasil dinonaktifkan.');
    }
}
