<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Promo;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private function computeStatus(Booking $booking): string
    {
        $now = Carbon::now();
        if ($now < $booking->start_time) return 'pending';
        if ($now > $booking->end_time) return 'completed';
        return 'ongoing';
    }

    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['venue', 'promo'])
            ->orderBy('start_time', 'desc')
            ->get()
            ->transform(function ($booking) {
                $booking->status = $this->computeStatus($booking);
                return $booking;
            });

        return response()->json($bookings);
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['venue', 'promo'])
            ->firstOrFail();

        $booking->status = $this->computeStatus($booking);

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'venue_id'       => 'required|integer|exists:venues,id',
            'promo_id'       => 'nullable|integer|exists:promos,id',
            'start_time'     => 'required|date|after:now',
            'end_time'       => 'required|date|after:start_time',
            'customer_name'  => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $venue     = Venue::findOrFail($validated['venue_id']);
        $startTime = Carbon::parse($validated['start_time']);
        $endTime   = Carbon::parse($validated['end_time']);

        // Durasi harus kelipatan 1 jam dan minimal 1 jam
        $durationInMinutes = $startTime->diffInMinutes($endTime);
        if ($durationInMinutes % 60 !== 0 || $durationInMinutes < 60) {
            return response()->json([
                'message' => 'Durasi harus minimal 1 jam dan kelipatan 1 jam.'
            ], 422);
        }

        $duration = $durationInMinutes / 60;

        // Cek conflict booking
        $conflict = Booking::where('venue_id', $venue->id)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Slot waktu sudah tidak tersedia.'
            ], 422);
        }

        // Hitung harga
        $basePrice  = $venue->price_per_hour * $duration;
        $totalPrice = $basePrice;

        if (!empty($validated['promo_id'])) {
            $promo = Promo::findOrFail($validated['promo_id']);
            $today = Carbon::today();

            // Cek promo masih aktif
            if ($today->lt($promo->start_date) || $today->gt($promo->end_date)) {
                return response()->json([
                    'message' => 'Promo sudah tidak aktif.'
                ], 422);
            }

            // Cek user sudah pernah pakai promo ini
            $alreadyUsed = Booking::where('user_id', $user->id)
                ->where('promo_id', $promo->id)
                ->exists();

            if ($alreadyUsed) {
                return response()->json([
                    'message' => 'Promo sudah pernah digunakan.'
                ], 422);
            }

            if ($promo->type === 'percent') {
                $totalPrice = $basePrice - ($basePrice * $promo->value / 100);
            } else {
                $totalPrice = $basePrice - $promo->value;
            }

            $totalPrice = max(0, $totalPrice);
        }

        $booking = Booking::create([
            'user_id'        => $user->id,
            'venue_id'       => $venue->id,
            'promo_id'       => $validated['promo_id'] ?? null,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'customer_name'  => $validated['customer_name'] ?? $user->username,
            'customer_phone' => $validated['customer_phone'] ?? $user->no_hp,
            'total_price'    => (int) $totalPrice,
            'rating'         => null,
        ]);

        return response()->json([
            'message' => 'Booking berhasil.',
            'booking' => $booking->load(['venue', 'promo']),
        ], 201);
    }

    public function rating(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($this->computeStatus($booking) !== 'completed') {
            return response()->json([
                'message' => 'Rating hanya bisa diberikan setelah booking selesai.'
            ], 422);
        }

        if (!is_null($booking->rating)) {
            return response()->json([
                'message' => 'Rating sudah pernah diberikan.'
            ], 422);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $booking->update(['rating' => $request->rating]);

        return response()->json([
            'message' => 'Rating berhasil diberikan.',
            'booking' => $booking,
        ]);
    }
}