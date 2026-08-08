<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueImage extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang boleh diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'venue_id',
        'image',
    ];

    /**
     * Relasi balik ke tabel Venue (Many to One)
     * Setiap gambar/foto ini adalah milik satu Venue
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}