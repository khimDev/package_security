<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackagePickup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PackagePickupController extends Controller
{
    // Menampilkan formulir pengambilan paket & area tanda tangan digital
    public function create(Package $package)
    {
        // Cegah jika paket sudah pernah diambil sebelumnya
        if ($package->pickup()->exists()) {
            return redirect()
                ->route('packages.show', $package)
                ->with('error', 'Paket ini sudah pernah diambil sebelumnya.');
        }

        return view('packages.pickup', compact('package'));
    }

    // Memproses data pengambilan dan menyimpan tanda tangan
    public function store(Request $request, Package $package)
    {
        // 1. Cek pencegahan pengambilan ganda (double-pickup prevention)
        if ($package->pickup()->exists()) {
            return redirect()
                ->route('packages.show', $package)
                ->with('error', 'Paket ini sudah pernah diambil sebelumnya.');
        }

        // 2. Validasi input
        $request->validate([
            'taker_name' => ['required', 'string', 'max:255'],
            'signature_data' => ['required', 'string', 'starts_with:data:image/png;base64,'],
        ], [
            'taker_name.required' => 'Nama pengambil paket wajib diisi.',
            'signature_data.required' => 'Tanda tangan digital wajib dibubuhkan pada area kanvas.',
            'signature_data.starts_with' => 'Format tanda tangan digital tidak valid.',
        ]);

        // 3. Ekstrak dan decode file gambar dari string Base64
        $signatureData = $request->input('signature_data');
        $imageParts = explode(';base64,', $signatureData);

        if (count($imageParts) < 2) {
            return back()
                ->withInput()
                ->withErrors(['signature_data' => 'Format gambar tanda tangan tidak valid.']);
        }

        $imageBase64 = base64_decode($imageParts[1]);

        if (!$imageBase64) {
            return back()
                ->withInput()
                ->withErrors(['signature_data' => 'Gagal memproses tanda tangan digital.']);
        }

        // 4. Pastikan direktori signatures ada di public storage
        if (!Storage::disk('public')->exists('signatures')) {
            Storage::disk('public')->makeDirectory('signatures');
        }

        // 5. Generate nama file unik & simpan ke storage
        $fileName = 'signatures/signature_' . $package->id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
        Storage::disk('public')->put($fileName, $imageBase64);

        // 6. Simpan catatan pengambilan ke database dengan transaksi
        DB::transaction(function () use ($package, $request, $fileName) {
            PackagePickup::create([
                'package_id' => $package->id,
                'taker_name' => trim($request->input('taker_name')),
                'served_by' => auth()->id(), // Otomatis dari ID satpam yang login
                'signature_path' => $fileName,
                'picked_up_at' => now(),     // Waktu server (WIB)
            ]);
        });

        return redirect()
            ->route('packages.show', $package)
            ->with('success', 'Pengambilan paket berhasil dikonfirmasi dan tanda tangan telah tersimpan.');
    }
}
