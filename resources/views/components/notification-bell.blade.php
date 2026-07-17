@php
$unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
$notifications = Auth::user()->notifications()->latest()->take(5)->get();
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-gray-500">
        <span class="sr-only">View notifications</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
        <div class="px-4 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
        </div>
        
        @forelse($notifications as $notification)
            <div class="px-4 py-3 hover:bg-gray-50 {{ $notification->is_read ? '' : 'bg-blue-50' }}">
                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
        @empty
            <div class="px-4 py-3 text-sm text-gray-500">No notifications</div>
        @endforelse
        
        <div class="px-4 py-2 border-t border-gray-200 text-center">
            <span class="text-sm text-gray-500">Latest notifications</span>
        </div>
    </div>
</div>
