<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagePickup extends Model
{
    protected $fillable = [
        'package_id',
        'taker_name',
        'served_by',
        'signature_path',
        'picked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'picked_up_at' => 'datetime',
        ];
    }

    // Data pengambilan milik satu paket
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Pengambilan paket di layani oleh satu penjaga
    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }
}
