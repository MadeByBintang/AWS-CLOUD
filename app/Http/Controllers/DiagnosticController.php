<?php

namespace App\Http\Controllers;

use App\Services\MiniStackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DiagnosticController extends Controller
{
    public function index(MiniStackService $miniStack)
    {
        $checks = [];

        // ── 1. Database ───────────────────────────────────────────
        try {
            DB::connection()->getPdo();
            $dbVersion = DB::select('SELECT VERSION() as v')[0]->v ?? '?';
            $checks['database'] = ['status' => 'ok', 'label' => 'Database MySQL', 'detail' => "Terhubung · v{$dbVersion}"];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'error', 'label' => 'Database MySQL', 'detail' => $e->getMessage()];
        }

        // ── 2. Cache ─────────────────────────────────────────────
        try {
            Cache::put('_diag_test', 'ok', 5);
            $hit = Cache::get('_diag_test') === 'ok';
            $checks['cache'] = ['status' => $hit ? 'ok' : 'warn', 'label' => 'Cache Driver', 'detail' => config('cache.default') . ($hit ? ' · berfungsi' : ' · miss')];
        } catch (\Exception $e) {
            $checks['cache'] = ['status' => 'error', 'label' => 'Cache Driver', 'detail' => $e->getMessage()];
        }

        // ── 3. MiniStack Ping ─────────────────────────────────────
        $url = env('MINISTACK_URL', 'http://localhost:4566');
        try {
            $t0  = microtime(true);
            $res = \Illuminate\Support\Facades\Http::timeout(3)->get($url);
            $ms  = round((microtime(true) - $t0) * 1000);
            $checks['ministack_ping'] = ['status' => 'ok', 'label' => 'MiniStack (Object Storage)', 'detail' => "Endpoint {$url} · {$ms}ms · HTTP {$res->status()}"];
        } catch (\Exception $e) {
            $checks['ministack_ping'] = ['status' => 'error', 'label' => 'MiniStack (Object Storage)', 'detail' => "Tidak bisa terhubung ke {$url}: " . $e->getMessage()];
        }

        // ── 4. Buat bucket test ───────────────────────────────────
        $testBucket = 'diag-healthcheck-' . Auth::id();
        try {
            $created = $miniStack->createBucket($testBucket);
            $checks['ministack_create'] = [
                'status' => $created ? 'ok' : 'error',
                'label'  => 'Create Bucket',
                'detail' => $created ? "Bucket `{$testBucket}` berhasil dibuat/ada" : 'createBucket() mengembalikan false',
            ];
        } catch (\Exception $e) {
            $checks['ministack_create'] = ['status' => 'error', 'label' => 'Create Bucket', 'detail' => $e->getMessage()];
        }

        // ── 5. Upload file test ───────────────────────────────────
        try {
            $content  = 'MiniStack health-check payload ' . now()->toISOString();
            $uploaded = $miniStack->uploadObject($testBucket, 'healthcheck.txt', $content, 'text/plain');
            $checks['ministack_upload'] = [
                'status' => $uploaded ? 'ok' : 'error',
                'label'  => 'Upload Object',
                'detail' => $uploaded ? "healthcheck.txt berhasil diunggah ke `{$testBucket}`" : 'uploadObject() mengembalikan false',
            ];
        } catch (\Exception $e) {
            $checks['ministack_upload'] = ['status' => 'error', 'label' => 'Upload Object', 'detail' => $e->getMessage()];
        }

        // ── 6. List objects ───────────────────────────────────────
        try {
            $objects = $miniStack->listObjects($testBucket);
            $found   = collect($objects)->firstWhere('key', 'healthcheck.txt');
            $checks['ministack_list'] = [
                'status' => $found ? 'ok' : 'warn',
                'label'  => 'List Objects',
                'detail' => count($objects) . ' objek ditemukan di bucket · ' . ($found ? 'healthcheck.txt ✓' : 'healthcheck.txt tidak ditemukan'),
            ];
        } catch (\Exception $e) {
            $checks['ministack_list'] = ['status' => 'error', 'label' => 'List Objects', 'detail' => $e->getMessage()];
        }

        // ── 7. Delete object test ─────────────────────────────────
        try {
            $deleted = $miniStack->deleteObject($testBucket, 'healthcheck.txt');
            $checks['ministack_delete'] = [
                'status' => $deleted ? 'ok' : 'warn',
                'label'  => 'Delete Object',
                'detail' => $deleted ? 'healthcheck.txt berhasil dihapus' : 'deleteObject() mengembalikan false',
            ];
        } catch (\Exception $e) {
            $checks['ministack_delete'] = ['status' => 'error', 'label' => 'Delete Object', 'detail' => $e->getMessage()];
        }

        // ── 8. Session ────────────────────────────────────────────
        try {
            $driver = config('session.driver');
            $checks['session'] = ['status' => 'ok', 'label' => 'Session Driver', 'detail' => "Driver: {$driver} · berjalan normal"];
        } catch (\Exception $e) {
            $checks['session'] = ['status' => 'error', 'label' => 'Session Driver', 'detail' => $e->getMessage()];
        }

        // ── Hitung total status ───────────────────────────────────
        $overall = collect($checks)->contains('status', 'error') ? 'error'
                 : (collect($checks)->contains('status', 'warn') ? 'warn' : 'ok');

        return view('diagnostic.index', compact('checks', 'overall', 'url'));
    }
}
