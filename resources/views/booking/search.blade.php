<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Search Trains
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form action="{{ route('booking.search') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Source Station --}}
                            <div>
                                <label for="source" class="block text-sm font-medium text-gray-700 mb-2">From Station</label>
                                <select name="source" id="source" required 
                                        class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                                    <option value="">Select Source</option>
                                    @foreach(\App\Models\Station::orderBy('name')->get() as $station)
                                        <option value="{{ $station->id }}">{{ $station->name }} ({{ $station->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Destination Station --}}
                            <div>
                                <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">To Station</label>
                                <select name="destination" id="destination" required 
                                        class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                                    <option value="">Select Destination</option>
                                    @foreach(\App\Models\Station::orderBy('name')->get() as $station)
                                        <option value="{{ $station->id }}">{{ $station->name }} ({{ $station->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="journey_date" class="block text-sm font-medium text-gray-700 mb-2">Journey Date</label>
                            <input type="date" name="journey_date" id="journey_date" required 
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="mt-1 block w-full py-3 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        {{-- Submit --}}
                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Search Trains
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>