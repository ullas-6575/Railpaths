<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-indigo-600">Train search</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $sourceStation->name }} <span class="text-indigo-500">→</span> {{ $destStation->name }}</h2>
            </div>
            <a href="{{ route('dashboard') }}" style="color:#4338ca" class="inline-flex items-center gap-2 text-sm font-semibold hover:opacity-75"><i class="bi bi-arrow-left"></i>Change search</a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-6xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white px-6 py-4 shadow-sm ring-1 ring-slate-200">
                <div><p class="text-sm font-semibold text-slate-900">Available trains</p><p class="mt-1 text-sm text-slate-500">Journey date: {{ \Illuminate\Support\Carbon::parse($journeyDate)->format('D, d M Y') }}</p></div>
                <span class="rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">{{ $trains->count() }} result(s)</span>
            </div>

            @if($trains->count() > 0)
                <div class="space-y-4">
                    @foreach($trains as $train)
                        @php
                            $sourceRoute = $train->routes->where('id', $sourceStation->id)->first();
                            $destRoute = $train->routes->where('id', $destStation->id)->first();
                            $sourcePivot = $sourceRoute?->pivot;
                            $destPivot = $destRoute?->pivot;
                        @endphp
                        <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="grid gap-0 lg:grid-cols-[1fr_250px]">
                                <div class="p-6 sm:p-7">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-xl font-bold text-slate-900">{{ $train->name }}</h3>
                                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">Train {{ $train->train_number }}</span>
                                    </div>
                                    <div class="mt-7 flex items-center gap-4 sm:gap-8">
                                        <div class="min-w-[90px]"><p class="text-2xl font-bold text-slate-900">{{ $sourcePivot?->departure_time ?? $sourcePivot?->arrival_time ?? '—' }}</p><p class="mt-1 text-sm font-medium text-slate-500">{{ $sourceStation->name }}</p></div>
                                        <div class="relative flex-1"><div class="border-t-2 border-dashed border-indigo-200"></div><span class="absolute left-1/2 top-2 -translate-x-1/2 whitespace-nowrap rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-600">{{ abs(($destPivot?->distance_from_source ?? 0) - ($sourcePivot?->distance_from_source ?? 0)) }} km</span></div>
                                        <div class="min-w-[90px] text-right"><p class="text-2xl font-bold text-slate-900">{{ $destPivot?->arrival_time ?? $destPivot?->departure_time ?? '—' }}</p><p class="mt-1 text-sm font-medium text-slate-500">{{ $destStation->name }}</p></div>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-between border-t border-slate-100 bg-slate-50 p-6 lg:border-l lg:border-t-0">
                                    <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ticket price</p><p class="mt-1 text-2xl font-bold text-indigo-600">৳{{ $train->base_fare ?? '100' }}</p><p class="text-xs text-slate-500">per passenger</p></div>
                                    <div class="mt-5"><p class="text-sm font-semibold {{ $train->available_seats > 0 ? 'text-emerald-700' : 'text-red-600' }}"><i class="bi bi-ticket-perforated me-1"></i>{{ $train->available_seats }} tickets available</p>
                                        @if($train->available_seats > 0)
                                            <a href="{{ route('booking.seats', ['train' => $train, 'source' => $sourceStation->id, 'destination' => $destStation->id, 'date' => $journeyDate]) }}" style="background:#4f46e5;color:#fff" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-bold shadow-sm hover:opacity-90"><i class="bi bi-ticket-perforated"></i>Book now</a>
                                        @else
                                            <span class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-500">Sold out</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl bg-white px-6 py-16 text-center shadow-sm ring-1 ring-slate-200"><i class="bi bi-train-front text-5xl text-slate-300"></i><h3 class="mt-4 text-lg font-bold text-slate-900">No trains found</h3><p class="mt-1 text-sm text-slate-500">Try another station pair or journey date.</p><a href="{{ route('dashboard') }}" style="background:#4f46e5;color:#fff" class="mt-5 inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold">Search again</a></div>
            @endif
        </div>
    </div>
</x-app-layout>
