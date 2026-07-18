@php
$unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
$notifications = Auth::user()->notifications()->latest()->take(5)->get();
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-indigo-600">
        <span class="sr-only">View notifications</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -right-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white ring-2 ring-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl bg-white py-1 shadow-xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <h3 class="text-sm font-bold text-slate-900">Notifications</h3>
            @if($unreadCount > 0)<span class="rounded-full bg-red-100 px-2 py-1 text-[11px] font-bold text-red-700">{{ $unreadCount }} unread</span>@endif
        </div>
        
        @forelse($notifications as $notification)
            <div class="border-b border-slate-100 px-4 py-3 hover:bg-slate-50 {{ $notification->is_read ? '' : 'bg-indigo-50' }}">
                <p class="text-sm font-bold text-slate-900">{{ $notification->title }}</p>
                <p class="mt-1 text-xs leading-5 text-slate-600">{{ $notification->message }}</p>
                <p class="mt-1 text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                @if(! $notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mt-2">
                        @csrf
                        <button class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Mark as read</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="px-4 py-6 text-center"><i class="bi bi-bell-slash text-2xl text-slate-300"></i><p class="mt-2 text-sm font-semibold text-slate-700">No notifications yet</p><p class="mt-1 text-xs text-slate-500">Delay alerts for today’s booked trains will appear here.</p></div>
        @endforelse
        
        <div class="px-4 py-2 border-t border-gray-200 text-center">
            <span class="text-xs font-medium text-slate-400">Latest notifications</span>
        </div>
    </div>
</div>
