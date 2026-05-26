<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StorageBucket;
use App\Models\ActivityLog;
use App\Services\MiniStackService; // Pastikan file service ini sudah kamu buat ya

class StorageController extends Controller
{
    protected $miniStack;

    // Inject MiniStackService agar bisa digunakan di controller ini
    public function __construct(MiniStackService $miniStack)
    {
        $this->miniStack = $miniStack;
    }

    /**
     * Menampilkan daftar bucket milik user.
     */
    public function index()
    {
        $user = Auth::user();
        $buckets = $user->storageBuckets()->latest()->get();

        return redirect()->route('dashboard'); // Sementara redirect ke dashboard
    }

    public function create()
    {
        // Tampilkan form pembuatan bucket baru
        // return view('storage.create');
        return view('storage.create'); // Pastikan kamu buat view ini untuk form pembuatan bucket
    }

    /**
     * Memproses form pembuatan bucket baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi input nama bucket
        $request->validate([
            'name' => ['required', 'string', 'max:40', 'regex:/^[a-z0-9\-]+$/']
        ]);

        $user = Auth::user();
        $subscription = $user->subscription;

        // 2. Cek apakah user punya paket langganan aktif
        if (!$subscription) {
            return back()->with('error', 'Silakan pilih paket langganan terlebih dahulu sebelum membuat bucket.');
        }

        // 3. Cek kuota bucket sesuai paket langganan
        $totalBuckets = $user->storageBuckets()->count();
        if ($totalBuckets >= $subscription->bucket_limit) {
            return back()->with('error', 'Batas maksimal bucket Anda sudah tercapai. Silakan upgrade paket layanan.');
        }

        // 4. Buat nama unik untuk di MiniStack (agar terisolasi & tidak bentrok antar user)
        // Format: user_{id}_{nama_bucket}
        $miniStackBucketName = 'user-' . $user->id . '-' . strtolower($request->name);

        // 5. Perintahkan MiniStack untuk membuat bucket
        $isCreated = $this->miniStack->createBucket($miniStackBucketName);

        // Jika MiniStack gagal merespons, batalkan proses
        if (!$isCreated) {
            return back()->with('error', 'Gagal terhubung ke MiniStack. Silakan coba lagi nanti.');
        }

        // 6. Jika berhasil di MiniStack, catat ke database Laravel
        StorageBucket::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'ministack_bucket_name' => $miniStackBucketName,
            'size_bytes' => 0, // Awalnya 0 bytes
            'is_active' => true,
        ]);

        // 7. Catat ke tabel log aktivitas sesuai spesifikasi PRD
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Create Bucket',
            'resource_type' => 'Storage',
            'resource_name' => $request->name,
            'ip_address' => $request->ip(),
            'metadata' => json_encode([
                'ministack_name' => $miniStackBucketName,
                'status' => 'success'
            ])
        ]);

        // Kembalikan user ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Bucket berhasil dibuat dan diisolasi di MiniStack!');
    }

    public function show(StorageBucket $bucket)
    {
        if ($bucket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $files = $this->miniStack->listObjects($bucket->ministack_bucket_name);

        return view('storage.show', [
            'bucket'     => $bucket,
            'files'      => $files,
            'miniStack'  => $this->miniStack,
        ]);
    }

    /**
     * Upload file ke dalam bucket.
     */
    public function upload(Request $request, StorageBucket $bucket)
    {
        if ($bucket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'file' => 'required|file|max:51200' // max 50MB
        ]);

        $file     = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $contents = file_get_contents($file->getRealPath());
        $mime     = $file->getMimeType();

        $uploaded = $this->miniStack->uploadObject(
            $bucket->ministack_bucket_name,
            $fileName,
            $contents,
            $mime
        );

        if (!$uploaded) {
            return back()->with('error', 'Gagal mengupload file ke MiniStack.');
        }

        // Update ukuran bucket
        $bucket->increment('size_bytes', $file->getSize());

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Upload File',
            'resource_type' => 'Storage',
            'resource_name' => $fileName,
            'ip_address'    => $request->ip(),
            'metadata'      => json_encode([
                'bucket'   => $bucket->name,
                'size'     => $file->getSize(),
                'mime'     => $mime,
            ])
        ]);

        return back()->with('success', "File {$fileName} berhasil diupload!");
    }

    /**
     * Hapus file dari bucket.
     */
    public function deleteObject(Request $request, StorageBucket $bucket)
    {
        if ($bucket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $fileName = $request->input('file_name');

        $deleted = $this->miniStack->deleteObject(
            $bucket->ministack_bucket_name,
            $fileName
        );

        if (!$deleted) {
            return back()->with('error', 'Gagal menghapus file.');
        }

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Delete File',
            'resource_type' => 'Storage',
            'resource_name' => $fileName,
            'ip_address'    => $request->ip(),
            'metadata'      => json_encode(['bucket' => $bucket->name])
        ]);

        return back()->with('success', "File {$fileName} berhasil dihapus.");
    }

    /**
     * Menghapus bucket.
     */
    public function destroy(StorageBucket $bucket)
    {
        // Pastikan hanya pemilik bucket yang bisa menghapus
        if ($bucket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Opsional: Tambahkan logika penghapusan di MiniStack lewat service
        // $this->miniStack->deleteBucket($bucket->ministack_bucket_name);

        $bucketName = $bucket->name;
        $bucket->delete();

        // Catat aktivitas penghapusan
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Delete Bucket',
            'resource_type' => 'Storage',
            'resource_name' => $bucketName,
            'ip_address' => request()->ip(),
            'metadata' => json_encode([])
        ]);

        return back()->with('success', 'Bucket berhasil dihapus.');
    }
}
