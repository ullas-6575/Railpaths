<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Available Trains: {{ $sourceStation->name }} → {{ $destStation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($trains->count() > 0)
                <div class="space-y-4">
                    @foreach($trains as $train)
                        @php
                            $sourceRoute = $train->routes->where('station_id', $sourceStation->id)->first();
                            $destRoute = $train->routes->where('station_id', $destStation->id)->first();
                            $schedule = $train->schedules->first();
                        @endphp
                        
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <h3 class="text-xl font-bold text-gray-900">{{ $train->name }}</h3>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $train->number }}</span>
                                        </div>
                                        
                                        <div class="mt-4 flex items-center space-x-8">
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-gray-900">{{ $sourceRoute->arrival_time }}</p>
                                                <p class="text-sm text-gray-500">{{ $sourceStation->name }}</p>
                                            </div>
                                            
                                            <div class="flex-1 flex items-center justify-center">
                                                <div class="border-t-2 border-gray-300 w-full relative">
                                                    <span class="absolute top-3 left-1/2 transform -translate-x-1/2 text-xs text-gray-500 whitespace-nowrap">
                                                        {{ $sourceRoute->distance_from_source - $destRoute->distance_from_source }} km
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-gray-900">{{ $destRoute->arrival_time }}</p>
                                                <p class="text-sm text-gray-500">{{ $destStation->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-8 text-right">
                                        <p class="text-2xl font-bold text-indigo-600">₹{{ $train->base_fare ?? '100' }}</p>
                                        <p class="text-sm text-gray-500">per person</p>
                                        
                                        <a href="{{ route('booking.seats', ['train' => $train, 'source' => $sourceStation->id, 'destination' => $destStation->id, 'date' => $journeyDate]) }}" 
                                           class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Check Seats
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No trains found</h3>
                        <p class="mt-1 text-sm text-gray-500">No trains available for this route on the selected date.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>