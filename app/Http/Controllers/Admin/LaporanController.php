<!-- <?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
public function downloadExcel(Request $request)
{
    // 1. Ambil Query Dasar (Tahun Ajaran 2024/2025)
    // $query = Tagihan::where('tahun', $request->tahun_ajaran); // Misal '2024/2025'

    // 2. Logika Filter Semester (Di sini kuncinya!)
    // if ($request->periode == 'ganjil') {
        // Ambil tagihan bulan Juli - Desember
        // $query->whereIn('bulan', ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
    } 
    // elseif ($request->periode == 'genap') {
        // Ambil tagihan bulan Januari - Juni
        // $query->whereIn('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni']);
    }

    // $data = $query->get();

    // 3. Download Excel
//     // return Excel::download(new SppExport($data), 'Laporan_SPP.xlsx');
// }
// } -->
