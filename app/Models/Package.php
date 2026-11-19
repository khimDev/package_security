<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Package extends Model
{
    protected $fillable = [
        'owner_name',
        'received_at',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
        ];
    }

    // Pembersihan otomatis: Hapus paket yang sudah diambil > 30 hari beserta file tanda tangannya
    public static function pruneExpiredPickups(): int
    {
        $expiredPackages = static::whereHas('pickup', function ($query) {
            $query->where('picked_up_at', '<', now()->subDays(30));
        })->with('pickup')->get();

        $count = 0;
        foreach ($expiredPackages as $package) {
            DB::transaction(function () use ($package) {
                if ($package->pickup) {
                    // Hapus file fisik gambar tanda tangan dari storage jika data lama berupa file path
                    if (
                        $package->pickup->signature_path &&
                        !str_starts_with($package->pickup->signature_path, 'data:image/') &&
                        Storage::disk('public')->exists($package->pickup->signature_path)
                    ) {
                        Storage::disk('public')->delete($package->pickup->signature_path);
                    }
                    $package->pickup->delete();
                }
                $package->delete();
            });
            $count++;
        }

        return $count;
    }

    // Accessor cerdas untuk menentukan status paket
    public function getStatusAttribute(): string
    {
        //Jika Sudah ada data pengambilan di tabel database
        if ($this->pickup()->exists()) {
            return 'Picked Up';
        }

        // Jika belum di ambil dan sudah lebih dari 2 bulan ( >= 60 hari)
        if ($this->received_at && $this->received_at->diffInDays(now()) >= 60) {
            return 'Perlu Tindak Lanjut';
        }

        // Status awal paket baru
        return 'Pending';
    }

    //Paket diterima oleh satu penjaga
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    //Paket memiliki satu data pengambilan
    public function pickup(): HasOne
    {
        return $this->hasOne(PackagePickup::class);
    }
}
