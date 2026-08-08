<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang boleh diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'user_id',
        'venue_id',
        'promo_id',
        'start_time',
        'end_time',
        'rating',
        'customer_name',
        'customer_phone',
        'total_price',
    ];

    /**
     * Penyesuaian tipe data pada kolom tertentu (Casting).
     */
    protected function casts(): array
    {
        return [
            'start_time'  => 'datetime', // Otomatis jadi objek Carbon (tanggal & jam)
            'end_time'    => 'datetime', // Otomatis jadi objek Carbon (tanggal & jam)
            'rating'      => 'integer',
            'total_price' => 'integer',
        ];
    }

    /**
     * Relasi ke tabel User (Many to One)
     * Booking ini dibuat oleh satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel Venue (Many to One)
     * Booking ini memesan satu Venue
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Relasi ke tabel Promo (Many to One)
     * Booking ini (mungkin) menggunakan satu Promo
     */
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}