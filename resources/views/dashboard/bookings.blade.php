@extends('layouts.app')

@section('title', 'Dashboard - BusBook')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg p-8 mb-8">
            <h1 class="text-4xl font-bold mb-2">Welcome, <?php echo htmlspecialchars($user['name']); ?>! 🚌</h1>
            <p class="text-blue-100">Manage your bus bookings and book new tickets</p>
        </div>

        <?php
        $pending = 0;
        $confirmed = 0;
        foreach ($bookings as $b) {
            if ($b['status'] === 'pending') $pending++;
            else if ($b['status'] === 'confirmed') $confirmed++;
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Bookings</p>
                        <p class="text-4xl font-bold text-blue-600"><?php echo count($bookings); ?></p>
                    </div>
                    <i class="fas fa-ticket-alt text-4xl text-blue-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Pending</p>
                        <p class="text-4xl font-bold text-yellow-600"><?php echo $pending; ?></p>
                    </div>
                    <i class="fas fa-hourglass-start text-4xl text-yellow-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Confirmed</p>
                        <p class="text-4xl font-bold text-green-600"><?php echo $confirmed; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-200"></i>
                </div>
            </div>
        </div>

        <!-- Book New Ticket Button -->
        <div class="mb-8">
            <a href="/DMS_BOOKING/dashboard/book" class="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transition">
                <i class="fas fa-plus mr-2"></i>Book New Ticket
            <?php if (empty($bookings)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-4">No bookings yet. Let's book your first bus ticket!</p>
                    <a href="/DMS_BOOKING/dashboard/book" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Book Now
                    </a>
                </div>
            <?php else: ?>
            @if ($bookings->isEmpty())
                <div class="p-8 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-4">No bookings yet. Let's book your first bus ticket!</p>
                    <a href="/DMS_BOOKING/dashboard/book" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Book Now
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">From</th>
                                <th class="px-6 py-4 text-left font-bold">To</th>
                                <th class="px-6 py-4 text-left font-bold">Date</th>
                                <th class="px-6 py-4 text-left font-bold">Seats</th>
                                <th class="px-6 py-4 text-left font-bold">Bus Type</th>
                                <th class="px-6 py-4 text-left font-bold">Price</th>
                                <th class="px-6 py-4 text-left font-bold">Status</th>
                                <th class="px-6 py-4 text-left font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($bookings as $booking)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $booking->from_location }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $booking->to_location }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $booking->journey_date }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $booking->number_of_seats }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold capitalize">
                                            {{ $booking->bus_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800">${{ number_format($booking->total_price, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if ($booking->status === 'pending')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">Pending</span>
                                        @elseif ($booking->status === 'confirmed')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Confirmed</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($booking['status'] === 'pending') { ?>
                                            <button onclick="if(confirm('Are you sure?')) window.location='/DMS_BOOKING/bookings/<?php echo $booking['id']; ?>/cancel'" 
                                                class="text-red-600 hover:text-red-800 font-bold transition">
                                                Cancel
                                            </button>
                                        <?php } else { ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php } ?>
                                    </td>
            <?php endif; ?>
        </div>
    </div>
         </table>
                </div>
            @endif
        </div>
    </div>
@endsection
