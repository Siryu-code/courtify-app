<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Kolom-kolom yang boleh diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'name',
        'type',
        'status',
        'price_per_hour',
        'location',
        'description',
        'image',
    ];

    /**
     * Penyesuaian tipe data pada kolom tertentu (Casting).
     */
    protected function casts(): array
    {
        return [
            'price_per_hour' => 'integer',
        ];
    }

    /**
     * Relasi ke tabel Booking (One to Many)
     * Satu Venue bisa dipesan berkali-kali (banyak Booking)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relasi ke tabel VenueImage (One to Many)
     * Satu Venue bisa punya banyak foto tambahan/galeri
     */
    public function images()
    {
        return $this->hasMany(VenueImage::class);
    }

    /**
     * Relasi ke tabel VenueFacility (One to Many)
     * Satu Venue bisa punya banyak fasilitas
     */
    public function facilities()
    {
        return $this->hasMany(VenueFacility::class);
    }
}