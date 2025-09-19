<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

if (!function_exists('multiDbTransaction')) {
    /**
     * Jalankan transaksi di beberapa database sekaligus.
     *
     * @param array    $connections   List nama connection (misal: ['mysql', 'mysql2'])
     * @param callable $callback      Fungsi yang menerima array transaksi
     * 
     * @return array
     */
    function multiDbTransaction(array $connections, callable $callback): array
    {
        $transactions = [];

        try {
            // Start semua transaction
            foreach ($connections as $conn) {
                $transactions[$conn] = DB::connection($conn);
                $transactions[$conn]->beginTransaction();
            }

            // Jalankan task user
            $result = $callback($transactions);

            // Commit semua
            foreach ($transactions as $trx) {
                $trx->commit();
            }

            return [
                'is_valid' => true,
                'message'  => 'Transaksi berhasil',
                'data'     => $result,
            ];
        } catch (\Throwable $e) {
            // Rollback semua jika ada error
            foreach ($transactions as $trx) {
                $trx->rollBack();
            }

            // Log error biar bisa dilihat di log-viewer
            Log::error('Multi DB Transaction gagal', [
                'connections' => $connections,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return [
                'is_valid' => false,
                'message'  => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'     => null,
            ];
        }
    }
}

/*
use Illuminate\Support\Facades\DB;

Route::get('/multi-transaction', function () {
    return multiDbTransaction(['mysql', 'mysql2'], function ($dbs) {
        // Insert ke DB default (mysql)
        $dbs['mysql']->table('users')->insert([
            'name' => 'Ali',
            'email' => 'ali@example.com'
        ]);

        // Insert ke DB lain (mysql2)
        $dbs['mysql2']->table('orders')->insert([
            'product' => 'Laptop',
            'qty' => 1
        ]);

        return "OK";
    });
});
*/