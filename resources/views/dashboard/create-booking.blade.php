@extends('layouts.app')

@section('title', 'Book a Ticket - BusBook')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-2 text-gray-800">Book a New Bus Ticket</h1>
            <p class="text-gray-600 mb-8">Fill in the details below to book your next journey</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">
                    <p class="font-bold mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- From Location -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">From Location</label>
                        <input type="text" name="from_location" value="{{ old('from_location') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('from_location') border-red-500 @enderror"
                            placeholder="e.g., New York">
                        @error('from_location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- To Location -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">To Location</label>
                        <input type="text" name="to_location" value="{{ old('to_location') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('to_location') border-red-500 @enderror"
                            placeholder="e.g., Boston">
                        @error('to_location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Journey Date -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Journey Date</label>
                        <input type="date" name="journey_date" value="{{ old('journey_date') }}" required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('journey_date') border-red-500 @enderror">
                        @error('journey_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Number of Seats -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Number of Seats</label>
                        <input type="number" name="number_of_seats" value="{{ old('number_of_seats', 1) }}" required min="1" max="10"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('number_of_seats') border-red-500 @enderror">
                        @error('number_of_seats')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bus Type -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Bus Type</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="bus_type" value="standard" @if(old('bus_type') === 'standard' || !old('bus_type')) checked @endif required class="mr-2">
                            <span class="inline-block px-4 py-2 border border-gray-300 rounded-lg hover:border-blue-500">
                                <span class="font-bold"><i class="fas fa-bus mr-2"></i>Standard</span>
                                <span class="text-gray-600 text-sm">($50/seat)</span>
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="bus_type" value="ac" @if(old('bus_type') === 'ac') checked @endif required class="mr-2">
                            <span class="inline-block px-4 py-2 border border-gray-300 rounded-lg hover:border-blue-500">
                                <span class="font-bold"><i class="fas fa-snowflake mr-2"></i>AC</span>
                                <span class="text-gray-600 text-sm">($75/seat)</span>
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="bus_type" value="sleeper" @if(old('bus_type') === 'sleeper') checked @endif required class="mr-2">
                            <span class="inline-block px-4 py-2 border border-gray-300 rounded-lg hover:border-blue-500">
                                <span class="font-bold"><i class="fas fa-bed mr-2"></i>Sleeper</span>
                                <span class="text-gray-600 text-sm">($100/seat)</span>
                            </span>
                        </label>
                    </div>
                    @error('bus_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Calculation -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Fare Summary</h3>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Price per seat:</span>
                        <span class="font-bold" id="pricePerSeat">$50</span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600">Number of seats:</span>
                        <span class="font-bold" id="seatCount">1</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total Price:</span>
                        <span class="text-2xl font-bold text-blue-600" id="totalPrice">$50</span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-check mr-2"></i>Confirm Booking
                    </button>
                    <a href="{{ route('bookings.index') }}" class="flex-1 bg-gray-300 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-400 transition text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const priceMap = { standard: 50, ac: 75, sleeper: 100 };
        
        function updatePrice() {
            const busType = document.querySelector('input[name="bus_type"]:checked').value;
            const seats = parseInt(document.querySelector('input[name="number_of_seats"]').value) || 1;
            const pricePerSeat = priceMap[busType];
            const total = pricePerSeat * seats;
            
            document.getElementById('pricePerSeat').textContent = '$' + pricePerSeat;
            document.getElementById('seatCount').textContent = seats;
            document.getElementById('totalPrice').textContent = '$' + total;
        }
        
        document.querySelectorAll('input[name="bus_type"]').forEach(el => el.addEventListener('change', updatePrice));
        document.querySelector('input[name="number_of_seats"]').addEventListener('change', updatePrice);
        updatePrice();
    </script>
@endsection
