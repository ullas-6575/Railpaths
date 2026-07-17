<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Station Master Dashboard - {{ $station->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Station Info Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $station->name }}</h3>
                            <p class="text-sm text-gray-500">Code: {{ $station->code }} | Platform: {{ $station->platform_count ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Today's Trains Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">Today's Incoming Trains ({{ now()->format('M d, Y') }})</h3>
                    
                    @if($incomingTrains->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Train</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delay</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($incomingTrains as $train)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $train->train->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $train->train->number }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">Arr: {{ $train->arrival_time }}</div>
                                                <div class="text-sm text-gray-500">Dep: {{ $train->departure_time }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($train->status === 'on_time')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">On Time</span>
                                                @elseif($train->status === 'delayed')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Delayed</span>
                                                @elseif($train->status === 'cancelled')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Cancelled</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($train->delay > 0)
                                                    <span class="text-red-600 font-bold">{{ $train->delay }} min</span>
                                                @else
                                                    <span class="text-green-600">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="openLogModal({{ $train->id }}, '{{ $train->train->name }}', '{{ $train->arrival_time }}', '{{ $train->departure_time }}')" 
                                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md">
                                                    Log Time
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No trains scheduled for today.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Log Modal --}}
    <div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Log Train Time</h3>
                <p id="modalTrainInfo" class="text-sm text-gray-500 mb-4"></p>
                
                <form id="logForm" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Actual Arrival Time</label>
                        <input type="time" name="actual_arrival" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Actual Departure Time</label>
                        <input type="time" name="actual_departure" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Remarks</label>
                        <textarea name="remarks" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    
                    <div class="flex justify-between">
                        <button type="button" onclick="closeLogModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLogModal(scheduleId, trainName, arrival, departure) {
            document.getElementById('modalTrainInfo').textContent = `${trainName} | Scheduled: ${arrival} - ${departure}`;
            document.getElementById('logForm').action = `/station-master/log-train/${scheduleId}`;
            document.getElementById('logModal').classList.remove('hidden');
        }

        function closeLogModal() {
            document.getElementById('logModal').classList.add('hidden');
        }
    </script>
</x-app-layout>