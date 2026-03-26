<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()->latest()->get();
        return view('dashboard.bookings', compact('bookings'));
    }

    public function create()
    {
        return view('dashboard.create-booking');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_location' => 'required|string',
            'to_location' => 'required|string',
            'journey_date' => 'required|date|after:today',
            'number_of_seats' => 'required|integer|min:1|max:10',
            'bus_type' => 'required|in:standard,ac,sleeper',
        ]);

        $pricePerSeat = match($validated['bus_type']) {
            'standard' => 50,
            'ac' => 75,
            'sleeper' => 100,
            default => 50,
        };

        $booking = Auth::user()->bookings()->create([
            ...$validated,
            'total_price' => $pricePerSeat * $validated['number_of_seats'],
            'status' => 'pending',
        ]);

        return redirect('/dashboard')->with('success', 'Booking created successfully!');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);
            return redirect('/dashboard')->with('success', 'Booking cancelled successfully!');
        }

        return redirect('/dashboard')->with('error', 'Cannot cancel this booking.');
    }
}
