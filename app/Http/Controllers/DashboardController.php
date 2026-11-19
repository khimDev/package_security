<?php

namespace App\Http\Controllers;

use App\Models\Package;

class DashboardController extends Controller
{
    public function index()
    {
        // Otomatis bersihkan paket yang sudah diambil > 30 hari
        Package::pruneExpiredPickups();

        $packages = Package::with(['receiver', 'pickup'])
           ->oldest()
           ->paginate(5);

        $totalPackages = Package::count();
        $pendingPackages = Package::whereDoesntHave('pickup')->count();
        $pickedUpPackages = Package::whereHas('pickup')->count();

        return view('dashboard', compact(
            'packages',
            'totalPackages',
            'pendingPackages',
            'pickedUpPackages'
        ));
    }
}
