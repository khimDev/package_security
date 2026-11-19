<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    // Menampilkan daftar paket yang belum diambil dengan fitur pencarian & pagination (10 per halaman)
    public function index(Request $request)
    {
        $query = Package::with(['receiver', 'pickup'])
            ->whereDoesntHave('pickup');

        if ($request->filled('search')) {
            $query->where('owner_name', 'like', '%' . $request->search . '%');
        }

        $packages = $query->oldest()->paginate(10);

        return view('packages.index', compact('packages'));
    }

    // Menampilkan riwayat paket yang sudah diambil dengan fitur pencarian & pagination
    public function history(Request $request)
    {
        // Otomatis bersihkan paket yang sudah diambil > 30 hari
        Package::pruneExpiredPickups();

        $query = Package::with(['receiver', 'pickup.server'])
            ->whereHas('pickup');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('owner_name', 'like', "%{$search}%")
                  ->orWhereHas('pickup', function ($qp) use ($search) {
                      $qp->where('taker_name', 'like', "%{$search}%");
                  });
            });
        }

        // Tampilkan dari yang paling baru diambil
        $packages = $query->latest()->paginate(10);

        return view('packages.history', compact('packages'));
    }

    // Menyimpan paket baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_name' => ['required', 'string', 'max:225'],
        ]);

        Package::create([
            'owner_name' => $validated['owner_name'],
            'received_at' => now(),
            'received_by' => auth()->id(),
        ]);

        return redirect('/')
            ->with('succes', 'Paket berhasil ditambahkan.');
    }

    // Menampilkan detail paket
    public function show(Package $package)
    {
        // Eager load petugaas penerima dan data pickup ( beserta satpam yang melayani )
        $package->load(['receiver', 'pickup.server']);

        return view('packages.show', compact('package'));
    }
}
