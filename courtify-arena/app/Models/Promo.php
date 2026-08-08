<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Kolom-kolom yang boleh diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'image',
        'name',
        'description',
        'type',
        'value',
        'start_date',
        'end_date',
    ];

    /**
     * Penyesuaian tipe data pada kolom tertentu (Casting).
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',   // Otomatis diubah jadi objek Carbon (tanggal)
            'end_date'   => 'date',   // Otomatis diubah jadi objek Carbon (tanggal)
            'value'      => 'integer',
        ];
    }

    /**
     * Relasi ke tabel Booking (One to Many)
     * Satu Promo bisa digunakan di banyak Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}