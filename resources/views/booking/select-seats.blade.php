<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Select Seats - {{ $train->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('booking.book') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="train_id" value="{{ $train->id }}">
                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                <input type="hidden" name="source_id" value="{{ request('source') }}">
                <input type="hidden" name="destination_id" value="{{ request('destination') }}">

                {{-- Seat Selection --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Select Your Seats</h3>
                        
                        <div class="grid grid-cols-4 gap-4 max-w-2xl mx-auto">
                            @foreach($seats as $seat)
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="seat_ids[]" value="{{ $seat->id }}" 
                                           class="peer sr-only" {{ $seat->is_available ? '' : 'disabled' }}>
                                    <div class="w-16 h-16 rounded-lg border-2 flex items-center justify-center text-sm font-medium transition-all
                                                {{ $seat->is_available 
                                                    ? 'border-gray-300 hover:border-indigo-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600' 
                                                    : 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed' }}">
                                        {{ $seat->seat_number }}
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center justify-center space-x-6 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded mr-2"></div>
                                <span>Available</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-indigo-600 rounded mr-2"></div>
                                <span>Selected</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-gray-100 border border-gray-200 rounded mr-2"></div>
                                <span>Booked</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Passenger Details --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Passenger Details</h3>
                        <div id="passengerDetails" class="space-y-4">
                            {{-- Dynamically populated based on seat selection --}}
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="bookBtn" disabled
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const checkboxes = document.querySelectorAll('input[name="seat_ids[]"]:not(:disabled)');
        const passengerDetails = document.getElementById('passengerDetails');
        const bookBtn = document.getElementById('bookBtn');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePassengerForms);
        });

        function updatePassengerForms() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            bookBtn.disabled = selected.length === 0;
            
            passengerDetails.innerHTML = '';
            
            selected.forEach((cb, index) => {
                const seatNum = cb.nextElementSibling.textContent.trim();
                passengerDetails.innerHTML += `
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="font-semibold mb-3">Passenger ${index + 1} - Seat ${seatNum}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="passenger_names[]" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Age</label>
                                <input type="number" name="passenger_ages[]" required min="1" max="120"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    </script>
</x-app-layout>