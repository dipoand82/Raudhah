<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Log;

trait HandlesExcelImports
{
    /**
     * Menangani proses import Excel dengan penanganan error terpusat.
     */
    public function importData($importClass, $file)
    {
        try {
            Excel::import($importClass, $file);
            
            return [
                'status'  => 'success',
                'message' => 'Data berhasil diimport ke dalam sistem.'
            ];

        } catch (ValidationException $e) {
            // Menangkap kesalahan validasi per baris (misal: NISN duplikat)
            return [
                'status'   => 'validation_error',
                'failures' => $e->failures()
            ];

        } catch (\Exception $e) {
            // Menangkap kesalahan sistem lainnya dan mencatatnya di log
            Log::error('Import Error: ' . $e->getMessage());
            
            return [
                'status'  => 'error',
                'message' => 'Terjadi kesalahan teknis: ' . $e->getMessage()
            ];
        }
    }
}