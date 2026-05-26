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
        $user       = Auth::user();
        $storageSub = $user->getOrCreateStorageSub();

        // Cek kuota: gunakan bucket_limit sebagai batas key (sesuai ERD tidak ada key_limit terpisah)
        $totalKeys = $user->credentials()->where('is_active', true)->count();
        $keyLimit  = $storageSub->bucket_limit ?? 2;

        if ($totalKeys >= $keyLimit) {
            return back()->with('error', 'Batas maksimal Access Key sudah tercapai. Silakan upgrade paket.');
        }

        // Minta kredensial ke MiniStack
        $miniStackUser = 'user_' . $user->id;
        $keys          = $this->miniStack->generateCredentials($miniStackUser);

        if (! $keys || ! isset($keys['access_key']) || ! isset($keys['secret_key'])) {
            return back()->with('error', 'Gagal men-generate kredensial dari MiniStack.');
        }

        // Simpan ke database (secret key dienkripsi)
        Credential::create([
            'user_id'      => $user->id,
            'service_type' => 's3',
            'name'         => $request->input('name', 'My Access Key'),
            'access_key'   => $keys['access_key'],
            'secret_key'   => Crypt::encryptString($keys['secret_key']),
            'permissions'  => ['s3:*'],
            'is_active'    => true,
        ]);

        ActivityLog::create([
            'user_id'       => $user->id,
            'action'        => 'Generate Credential',
            'resource_type' => 'Access Key',
            'resource_name' => $keys['access_key'],
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => $request->ip(),
            'metadata'      => ['ministack_user' => $miniStackUser],
        ]);

        return redirect()->route('dashboard')->with('success', 'Access Key baru berhasil dibuat!');
    }

    /**
     * Menampilkan Secret Key secara aman (hanya saat tombol Reveal diklik)
     */
    public function reveal(Credential $cred)
    {
        if ($cred->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $secretKey = Crypt::decryptString($cred->secret_key);

            ActivityLog::create([
                'user_id'       => Auth::id(),
                'action'        => 'Reveal Secret Key',
                'resource_type' => 'Access Key',
                'resource_name' => $cred->access_key,
                'device_type'   => 'web',
                'status'        => 'success',
                'ip_address'    => request()->ip(),
                'metadata'      => [],
            ]);

            return response()->json([
                'success'    => true,
                'secret_key' => $secretKey,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendekripsi Secret Key.',
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
        $storageSub  = $user->getOrCreateStorageSub();
        $keyLimit    = $storageSub?->bucket_limit ?? 2;

        return view('credentials.index', compact('credentials', 'keyLimit'));
    }

    /**
     * Tampilkan form buat key baru (redirect ke index).
     */
    public function create()
    {
        return redirect()->route('credentials.index');
    }

    /**
     * Menonaktifkan / mencabut access key.
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
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => request()->ip(),
            'metadata'      => [],
        ]);

        return back()->with('success', 'Access Key berhasil dinonaktifkan.');
    }
}
