<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700"><i class="bi bi-search text-lg"></i></span>
            <div><p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Journey planner</p><h2 class="text-2xl font-bold text-slate-900">Find your train</h2></div>
        </div>
    </x-slot>

    @php($stations = \App\Models\Station::all()->sortBy(fn ($station) => $station->railOrder())->values())
    <div class="min-h-screen bg-slate-50 py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl shadow-xl" style="background:linear-gradient(120deg,#1e1b4b,#4338ca 60%,#6366f1)">
                <div class="grid gap-8 p-7 sm:p-10 lg:grid-cols-[.9fr_1.4fr] lg:items-center">
                    <div class="text-white">
                        <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-widest text-indigo-100">Track Rail</span>
                        <h1 class="mt-5 text-3xl font-bold leading-tight sm:text-4xl">Your next journey starts here.</h1>
                        <p class="mt-4 max-w-sm text-sm leading-6 text-indigo-100">Search trains, compare schedules, choose your seats, and travel with confidence.</p>
                        <div class="mt-7 flex flex-wrap gap-3 text-xs font-medium text-indigo-100"><span class="rounded-full border border-white/20 px-3 py-2">Fixed forward routes</span><span class="rounded-full border border-white/20 px-3 py-2">Live delay alerts</span></div>
                    </div>

                    <form action="{{ route('booking.search') }}" method="POST" class="rounded-2xl bg-white p-5 shadow-2xl sm:p-7">
                        @csrf
                        <div class="mb-6"><h3 class="text-lg font-bold text-slate-900">Search available trains</h3><p class="mt-1 text-sm text-slate-500">Choose stations in Rangpur → Chattogram order.</p></div>
                        @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label for="source" class="mb-2 block text-sm font-semibold text-slate-700">From</label><select name="source" id="source" required class="block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500"><option value="">Select departure station</option>@foreach($stations as $station)<option value="{{ $station->id }}" @selected(old('source') == $station->id)>{{ $station->railOrder() }}. {{ $station->name }} ({{ $station->code }})</option>@endforeach</select></div>
                            <div><label for="destination" class="mb-2 block text-sm font-semibold text-slate-700">To</label><select name="destination" id="destination" required class="block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500"><option value="">Select arrival station</option>@foreach($stations as $station)<option value="{{ $station->id }}" @selected(old('destination') == $station->id)>{{ $station->railOrder() }}. {{ $station->name }} ({{ $station->code }})</option>@endforeach</select></div>
                        </div>
                        <div class="mt-4"><label for="journey_date" class="mb-2 block text-sm font-semibold text-slate-700">Journey date</label><input type="date" name="journey_date" id="journey_date" value="{{ old('journey_date') }}" min="{{ now()->toDateString() }}" required class="block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500"></div>
                        <button type="submit" style="background:#4f46e5;color:#fff" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-3.5 text-sm font-bold shadow-lg transition hover:opacity-90"><i class="bi bi-search"></i>Search trains</button>
                    </form>
                </div>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-3"><div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><i class="bi bi-ticket-perforated text-xl text-indigo-600"></i><p class="mt-3 font-bold text-slate-900">Easy booking</p><p class="mt-1 text-sm text-slate-500">Select seats in a simple layout.</p></div><div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><i class="bi bi-clock-history text-xl text-indigo-600"></i><p class="mt-3 font-bold text-slate-900">Clear schedules</p><p class="mt-1 text-sm text-slate-500">Compare departure and arrival times.</p></div><div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><i class="bi bi-bell text-xl text-indigo-600"></i><p class="mt-3 font-bold text-slate-900">Delay alerts</p><p class="mt-1 text-sm text-slate-500">Stay updated through your bell.</p></div></div>
        </div>
    </div>
</x-app-layout>
