<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-emerald-600">Operations center</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Station Master Dashboard</h2>
            </div>
            @if($station)
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>{{ $station->name }} station</span>
            @endif
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800"><i class="bi bi-check-circle mr-2"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800">{{ session('error') }}</div>
            @endif

            @if(!$station)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-2xl text-amber-700"><i class="bi bi-geo-alt"></i></div>
                    <h3 class="mt-4 text-xl font-bold text-slate-900">Station assignment required</h3>
                    <p class="mx-auto mt-2 max-w-lg text-sm text-slate-600">Your account is approved, but no station is assigned yet. Ask an administrator to assign your operating station.</p>
                </div>
            @else
                <section class="overflow-hidden rounded-2xl p-7 text-white shadow-lg" style="background: linear-gradient(120deg, #064e3b 0%, #047857 55%, #10b981 100%);">
                    <div class="flex flex-wrap items-end justify-between gap-6">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-100">Today, {{ now()->format('d M Y') }}</p>
                            <h3 class="mt-2 text-3xl font-bold">{{ $station->name }} station</h3>
                            <p class="mt-2 max-w-xl text-emerald-100">Log actual train times and keep passengers informed about delays.</p>
                        </div>
                        <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wider text-emerald-100">Trains today</p>
                            <p class="mt-1 text-3xl font-bold">{{ $incomingTrains->count() }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Today’s train movements</h3>
                            <p class="mt-1 text-sm text-slate-500">Record the actual arrival or departure for each scheduled train.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $incomingTrains->where('status', 'pending')->count() }} pending</span>
                    </div>
                    @if($incomingTrains->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50"><tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Train</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Scheduled</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Delay</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
                                </tr></thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($incomingTrains as $schedule)
                                        <tr class="hover:bg-emerald-50/40">
                                            <td class="whitespace-nowrap px-6 py-4"><div class="font-semibold text-slate-900">{{ $schedule->train->name }}</div><div class="text-xs text-slate-500">{{ $schedule->train->train_number }}</div></td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"><div>Arr: {{ $schedule->arrival_time ?? '—' }}</div><div class="text-xs text-slate-400">Dep: {{ $schedule->departure_time ?? '—' }}</div></td>
                                            <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $schedule->status === 'delayed' ? 'bg-red-100 text-red-700' : ($schedule->status === 'on_time' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $schedule->status)) }}</span></td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold {{ $schedule->delay > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $schedule->delay > 0 ? $schedule->delay . ' min' : '—' }}</td>
                                            <td class="px-6 py-4">
                                                <form method="POST" action="{{ route('station-master.log-train', $schedule->id) }}" class="flex items-center justify-end gap-2">
                                                    @csrf
                                                    <input type="time" name="actual_departure" required value="{{ $schedule->log?->actual_departure ? \Illuminate\Support\Carbon::parse($schedule->log->actual_departure)->format('H:i') : '' }}" aria-label="Actual departure time" class="w-32 rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                    <button type="submit" style="background:#059669;color:#fff" class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold shadow-sm hover:opacity-90"><i class="bi bi-check-lg me-2"></i>Save departure</button>
                                                    <button type="button" onclick="openLogModal({{ $schedule->id }}, @js($schedule->train->name), @js($schedule->arrival_time ?? $schedule->departure_time), @js($schedule->departure_time))" style="background:#ecfdf5;color:#047857" class="inline-flex items-center rounded-lg border border-emerald-200 px-3 py-2 text-sm font-semibold hover:opacity-80" title="Log arrival and remarks"><i class="bi bi-three-dots"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-14 text-center"><i class="bi bi-calendar2-check text-4xl text-slate-300"></i><p class="mt-3 font-semibold text-slate-700">No trains scheduled today</p><p class="mt-1 text-sm text-slate-500">New schedules will appear here automatically.</p></div>
                    @endif
                </section>
            @endif
        </div>
    </div>

    <div id="logModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 p-4 backdrop-blur-sm">
        <div class="mx-auto mt-16 max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between"><div><p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Train movement</p><h3 class="mt-1 text-xl font-bold text-slate-900">Log actual time</h3></div><button type="button" onclick="closeLogModal()" class="text-2xl text-slate-400 hover:text-slate-700">&times;</button></div>
            <p id="modalTrainInfo" class="mt-3 rounded-lg bg-slate-50 p-3 text-sm text-slate-600"></p>
            <form id="logForm" method="POST" class="mt-5 space-y-4">
                @csrf
                <div><label class="block text-sm font-semibold text-slate-700">Actual arrival</label><input type="time" name="actual_arrival" class="mt-1 block w-full rounded-lg border-slate-300" autofocus></div>
                <div><label class="block text-sm font-semibold text-slate-700">Actual departure</label><input type="time" name="actual_departure" class="mt-1 block w-full rounded-lg border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700">Remarks <span class="font-normal text-slate-400">(optional)</span></label><textarea name="remarks" rows="2" class="mt-1 block w-full rounded-lg border-slate-300"></textarea></div>
                <div class="flex justify-end gap-3 pt-2"><button type="button" onclick="closeLogModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</button><button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save update</button></div>
            </form>
        </div>
    </div>
    <script>
        function openLogModal(id, train, arrival, departure) { document.getElementById('modalTrainInfo').textContent = `${train} · Scheduled arrival ${arrival ?? '—'} · departure ${departure ?? '—'}`; document.getElementById('logForm').action = `/station-master/log-train/${id}`; document.getElementById('logModal').classList.remove('hidden'); }
        function closeLogModal() { document.getElementById('logModal').classList.add('hidden'); }
    </script>
</x-app-layout>
