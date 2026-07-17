<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Station Logs - Admin View
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex space-x-4">
                        <select name="station_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Stations</option>
                            @foreach($stations as $station)
                                <option value="{{ $station->id }}" {{ request('station_id') == $station->id ? 'selected' : '' }}>
                                    {{ $station->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}"
                               class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="on_time" {{ request('status') == 'on_time' ? 'selected' : '' }}>On Time</option>
                            <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Filter</button>
                    </form>
                </div>
            </div>

            {{-- Logs Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Station</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Train</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Station Master</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled/Actual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $log->station->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->train->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->stationMaster->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>Arr: {{ $log->scheduled_arrival }} / {{ $log->actual_arrival ?? '--:--' }}</div>
                                        <div>Dep: {{ $log->scheduled_departure }} / {{ $log->actual_departure ?? '--:--' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $log->delay_minutes > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $log->delay_minutes > 0 ? $log->delay_minutes . ' min' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $log->status === 'on_time' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $log->status === 'delayed' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $log->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>