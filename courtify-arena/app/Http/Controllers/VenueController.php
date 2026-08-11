<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        $query = Venue::with(['images', 'facilities'])
            ->withAvg('bookings', 'rating')
            ->withCount(['bookings as ongoing_count' => function ($q) use ($now) {
                $q->where('start_time', '<=', $now)
                  ->where('end_time', '>=', $now);
            }]);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        match ($request->get('sort', 'name_asc')) {
            'name_desc'  => $query->orderBy('name', 'desc'),
            'price_asc'  => $query->orderBy('price_per_hour', 'asc'),
            'price_desc' => $query->orderBy('price_per_hour', 'desc'),
            default      => $query->orderBy('name', 'asc'),
        };

        $venues = $query->get()->transform(function ($venue) {
            $venue->computed_status = $venue->ongoing_count > 0
                ? 'ongoing'
                : $venue->status;

            $venue->rating = $venue->bookings_avg_rating
                ? round($venue->bookings_avg_rating, 1)
                : null;

            return $venue->makeHidden(['ongoing_count', 'bookings_avg_rating']);
        });

        return response()->json($venues);
    }

    public function show($id)
    {
        $now = Carbon::now();

        $venue = Venue::with(['images', 'facilities'])
            ->withAvg('bookings', 'rating')
            ->withCount(['bookings as ongoing_count' => function ($q) use ($now) {
                $q->where('start_time', '<=', $now)
                  ->where('end_time', '>=', $now);
            }])
            ->findOrFail($id);

        $venue->computed_status = $venue->ongoing_count > 0
            ? 'ongoing'
            : $venue->status;

        $venue->rating = $venue->bookings_avg_rating
            ? round($venue->bookings_avg_rating, 1)
            : null;

        return response()->json(
            $venue->makeHidden(['ongoing_count', 'bookings_avg_rating'])
        );
    }

    public function slots(Request $request, $id)
    {
        $request->validate([
            'date'     => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:1|max:23',
        ]);

        $venue    = Venue::findOrFail($id);
        $date     = Carbon::parse($request->date)->startOfDay();
        $duration = (int) $request->duration;
        $now      = Carbon::now();

        // Ambil semua booking yang overlap dengan hari yang dipilih
        $bookings = $venue->bookings()
            ->where('start_time', '<', $date->copy()->addDay())
            ->where('end_time', '>', $date)
            ->get(['start_time', 'end_time']);

        $availableSlots = [];

        for ($hour = 0; $hour <= 24 - $duration; $hour++) {
            $slotStart = $date->copy()->addHours($hour);
            $slotEnd   = $slotStart->copy()->addHours($duration);

            // Skip slot yang sudah lewat kalau date = hari ini
            if ($slotStart <= $now) {
                continue;
            }

            $isConflict = false;

            foreach ($bookings as $booking) {
                // Overlap: slot_start < booking_end && slot_end > booking_start
                if ($slotStart < $booking->end_time && $slotEnd > $booking->start_time) {
                    $isConflict = true;
                    break;
                }
            }

            if (!$isConflict) {
                $availableSlots[] = [
                    'start' => $slotStart->format('H:i'),
                    'end'   => $slotEnd->format('H:i'),
                ];
            }
        }

        return response()->json([
            'venue_id' => $venue->id,
            'date'     => $date->toDateString(),
            'duration' => $duration,
            'slots'    => $availableSlots,
        ]);
    }
}