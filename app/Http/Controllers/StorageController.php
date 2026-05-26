<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StorageBucket;
use App\Models\StorageObject;
use App\Models\ActivityLog;
use App\Services\MiniStackService;

class StorageController extends Controller
{
    protected $miniStack;

    public function __construct(MiniStackService $miniStack)
    {
        $this->miniStack = $miniStack;
    }

    /**
     * Menampilkan daftar bucket milik user.
     */
    public function index()
    {
        $user       = Auth::user();
        $storageSub = $user->getOrCreateStorageSub();
        $buckets    = $user->storageBuckets()->latest()->get();

        return view('storage.index', compact('storageSub', 'buckets'));
    }

    public function create()
    {
        return view('storage.create');
    }

    /**
     * Memproses form pembuatan bucket baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:40', 'regex:/^[a-z0-9\-]+$/']
        ]);

        $user       = Auth::user();
        $storageSub = $user->getOrCreateStorageSub();

        $totalBuckets = $user->storageBuckets()->count();
        if ($totalBuckets >= $storageSub->bucket_limit) {
            return back()->with('error', 'Batas maksimal bucket Anda sudah tercapai. Silakan upgrade paket layanan.');
        }

        // Buat nama unik untuk MiniStack
        $miniStackName = 'user-' . $user->id . '-' . strtolower($request->name);

        $isCreated = $this->miniStack->createBucket($miniStackName);

        if (! $isCreated) {
            return back()->with('error', 'Gagal terhubung ke MiniStack. Silakan coba lagi nanti.');
        }

        StorageBucket::create([
            'user_id'       => $user->id,
            'name'          => $request->name,
            'ministack_name' => $miniStackName,
            'region'        => $request->input('region', 'ap-southeast-1'),
            'is_public'     => false,
            'versioning'    => false,
            'size_bytes'    => 0,
            'object_count'  => 0,
            'is_active'     => true,
        ]);

        ActivityLog::create([
            'user_id'       => $user->id,
            'action'        => 'Create Bucket',
            'resource_type' => 'Storage',
            'resource_name' => $request->name,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => $request->ip(),
            'metadata'      => ['ministack_name' => $miniStackName],
        ]);

        return redirect()->route('dashboard')->with('success', 'Bucket berhasil dibuat!');
    }

    public function show(StorageBucket $bucket)
    {
        if ($bucket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $files = $this->miniStack->listObjects($bucket->ministack_name);

        return view('storage.show', [
            'bucket'    => $bucket,
            'files'     => $files,
            'miniStack' => $this->miniStack,
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
            'file' => 'required|file|max:51200'
        ]);

        $file     = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $contents = file_get_contents($file->getRealPath());
        $mime     = $file->getMimeType();

        $uploaded = $this->miniStack->uploadObject(
            $bucket->ministack_name,
            $fileName,
            $contents,
            $mime
        );

        if (! $uploaded) {
            return back()->with('error', 'Gagal mengupload file ke MiniStack.');
        }

        // Simpan record objek ke tabel storage_objects
        StorageObject::create([
            'bucket_id'     => $bucket->id,
            'user_id'       => Auth::id(),
            'object_key'    => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'content_type'  => $mime,
            'size_bytes'    => $file->getSize(),
            'storage_class' => 'STANDARD',
            'is_deleted'    => false,
            'uploaded_at'   => now(),
        ]);

        // Update ukuran & object_count bucket
        $bucket->increment('size_bytes', $file->getSize());
        $bucket->increment('object_count');

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Upload File',
            'resource_type' => 'Storage',
            'resource_name' => $fileName,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => $request->ip(),
            'metadata'      => [
                'bucket' => $bucket->name,
                'size'   => $file->getSize(),
                'mime'   => $mime,
            ],
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
            $bucket->ministack_name,
            $fileName
        );

        if (! $deleted) {
            return back()->with('error', 'Gagal menghapus file.');
        }

        // Soft-delete record di storage_objects
        $obj = StorageObject::where('bucket_id', $bucket->id)
            ->where('object_key', $fileName)
            ->first();

        if ($obj) {
            $bucket->decrement('size_bytes', $obj->size_bytes);
            $bucket->decrement('object_count');
            $obj->update(['is_deleted' => true]);
        }

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Delete File',
            'resource_type' => 'Storage',
            'resource_name' => $fileName,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => $request->ip(),
            'metadata'      => ['bucket' => $bucket->name],
        ]);

        return back()->with('success', "File {$fileName} berhasil dihapus.");
    }

    /**
     * Menghapus bucket.
     */
    public function destroy(StorageBucket $bucket)
    {
        if ($bucket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $bucketName = $bucket->name;
        $bucket->delete();

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Delete Bucket',
            'resource_type' => 'Storage',
            'resource_name' => $bucketName,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => request()->ip(),
            'metadata'      => [],
        ]);

        return back()->with('success', 'Bucket berhasil dihapus.');
    }
}
