<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Passenger portal</p>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Good to see you, {{ auth()->user()->name }}</h2>
            </div>
            <span class="hidden sm:inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">Ready to travel</span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            <section class="overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(120deg, #312e81 0%, #4338ca 55%, #6366f1 100%);">
                <div class="grid gap-8 px-6 py-8 text-white md:grid-cols-[1fr_1.4fr] md:px-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-200">Plan your next journey</p>
                        <h3 class="mt-3 text-3xl font-bold">Book a seat in a few clicks.</h3>
                        <p class="mt-3 max-w-md text-indigo-100">Choose a forward route from the fixed Rangpur → Chattogram station line.</p>
                    </div>
                    <form action="{{ route('booking.search') }}" method="POST" class="rounded-xl bg-white p-5 text-gray-900 shadow-lg">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="source" class="block text-sm font-medium">From</label>
                                <select name="source" id="source" required class="mt-1 block w-full rounded-lg border-gray-300">
                                    <option value="">Select station</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->railOrder() }}. {{ $station->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="destination" class="block text-sm font-medium">To</label>
                                <select name="destination" id="destination" required class="mt-1 block w-full rounded-lg border-gray-300">
                                    <option value="">Select station</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->railOrder() }}. {{ $station->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="journey_date" class="block text-sm font-medium">Journey date</label>
                            <input type="date" name="journey_date" id="journey_date" min="{{ now()->toDateString() }}" required class="mt-1 block w-full rounded-lg border-gray-300">
                        </div>
                        <button class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 font-semibold text-white shadow-sm hover:bg-indigo-500"><i class="bi bi-search"></i>Search trains</button>
                    </form>
                </div>
            </section>

            <div class="grid gap-8 lg:grid-cols-[1.6fr_1fr]">
                <section id="booking" class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Upcoming bookings</h3>
                        <a href="#booking" class="text-sm font-medium text-indigo-600">Book another trip</a>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($bookings as $booking)
                            <a href="{{ route('booking.confirmation', $booking) }}" class="block rounded-lg border border-gray-200 p-4 hover:border-indigo-300 hover:bg-indigo-50">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $booking->route?->train?->name ?? 'Train' }}</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ $booking->sourceStation?->name }} → {{ $booking->destinationStation?->name }}</p>
                                    </div>
                                    <p class="text-right text-sm font-medium text-gray-700">{{ $booking->travel_date->format('M d, Y') }}<br><span class="text-xs text-gray-500">{{ $booking->seat_count }} seat(s)</span></p>
                                </div>
                            </a>
                        @empty
                            <p class="rounded-lg bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">Your confirmed trips will appear here.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Recent notifications</h3>
                    <div class="mt-5 space-y-4">
                        @forelse($notifications as $notification)
                            <div class="border-l-4 {{ $notification->is_read ? 'border-gray-200' : 'border-indigo-500' }} pl-3">
                                <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $notification->message }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">You’ll see delay alerts here when a booked train is affected.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
