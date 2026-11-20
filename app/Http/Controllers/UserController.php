<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Package;
use App\Models\PackagePickup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Tampilkan daftar akun petugas & admin
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        })
        ->withCount(['receivedPackages', 'servedPickups'])
        ->orderBy('id', 'asc')
        ->paginate(10)
        ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Tambah akun petugas baru oleh Admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:4',
        ], [
            'name.required' => 'Nama petugas wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah dipakai petugas lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('users.index')->with('success', "Akun petugas {$validated['name']} berhasil dibuat.");
    }

    /**
     * Reset password petugas
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'nullable|string|min:4',
        ], [
            'new_password.min' => 'Password baru minimal 4 karakter.',
        ]);

        $newPassword = $request->filled('new_password') ? $request->input('new_password') : 'satpam123';

        $user->update([
            'password' => $newPassword,
        ]);

        return redirect()->back()->with('success', "Password untuk {$user->name} berhasil direset menjadi: {$newPassword}");
    }

    /**
     * Hapus akun petugas
     */
    public function destroy(User $user)
    {
        // Cegah menghapus akun yang sedang login
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cek integritas relasi: jika ada riwayat paket atau serah terima
        if ($user->receivedPackages()->exists() || $user->servedPickups()->exists()) {
            return redirect()->back()->with('error', "Petugas {$user->name} tidak dapat dihapus karena tercatat dalam riwayat paket/pengambilan.");
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Akun petugas {$nama} berhasil dihapus.");
    }

    /**
     * Bersihkan seluruh data paket & pickup testing, lalu daftarkan 7 akun satpam resmi kampus
     */
    public function resetTestingData()
    {
        DB::transaction(function () {
            // 1. Bersihkan seluruh data paket dan riwayat pickup
            PackagePickup::query()->delete();
            Package::query()->delete();

            // 2. Bersihkan akun satpam testing lama (selain akun admin yang sedang login)
            User::where('id', '!=', auth()->id())
                ->where('is_admin', false)
                ->delete();

            // 3. Daftarkan 7 akun satpam resmi Universitas Widyagama Husada Malang
            $officialSatpams = [
                ['name' => 'LINDUNG AGUNG', 'username' => 'lindung'],
                ['name' => 'FERY', 'username' => 'fery'],
                ['name' => 'RISKY N', 'username' => 'risky_n'],
                ['name' => 'RISKY SAIFUL', 'username' => 'risky_saiful'],
                ['name' => 'AMIN', 'username' => 'amin'],
                ['name' => 'FRANDY', 'username' => 'frandy'],
                ['name' => 'RAHMAD', 'username' => 'rahmad'],
            ];

            foreach ($officialSatpams as $satpam) {
                User::updateOrCreate(
                    ['username' => $satpam['username']],
                    [
                        'name' => $satpam['name'],
                        'password' => 'satpam123',
                        'is_admin' => false,
                    ]
                );
            }
        });

        return redirect()->route('users.index')->with('success', 'Database berhasil dibersihkan! 7 akun satpam resmi kampus telah siap digunakan.');
    }
}
