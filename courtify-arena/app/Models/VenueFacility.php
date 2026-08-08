<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueFacility extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang boleh diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'venue_id',
        'name',
        'icon',
        'quantity',
    ];

    /**
     * Penyesuaian tipe data pada kolom tertentu (Casting).
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * Relasi balik ke tabel Venue (Many to One)
     * Setiap fasilitas ini adalah milik satu Venue
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}