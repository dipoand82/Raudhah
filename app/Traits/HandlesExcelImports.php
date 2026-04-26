<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

trait HandlesExcelImports
{
    public function importData($importClass, $file)
    {
        try {
            Excel::import($importClass, $file);

            return [
                'status' => 'success',
                'message' => 'Data berhasil diimport ke dalam sistem.',
            ];

        } catch (ValidationException $e) {
            return [
                'status' => 'validation_error',
                'failures' => $e->failures(),
            ];

        } catch (\Exception $e) {
            Log::error('Import Error: '.$e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Terjadi kesalahan teknis: '.$e->getMessage(),
            ];
        }
    }
}
