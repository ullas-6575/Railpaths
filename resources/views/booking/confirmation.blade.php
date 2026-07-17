<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Booking confirmed</h2></x-slot>
    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <p class="text-lg">Your booking reference is <strong>{{ $booking->id }}</strong>.</p>
            <a class="mt-4 inline-block text-indigo-600" href="{{ route('dashboard') }}">Return to dashboard</a>
        </div>
    </div></div>
</x-app-layout>
