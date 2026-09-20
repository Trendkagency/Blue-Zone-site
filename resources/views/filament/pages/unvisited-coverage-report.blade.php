<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Visit Cycle</label>
                    <select wire:model.live="selectedCycleId" class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-1.5 px-3">
                        @foreach($this->cycles as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->status) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Representative</label>
                    <select wire:model.live="selectedMrId" class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-1.5 px-3">
                        <option value="">All Medical Reps</option>
                        @foreach($this->reps as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Doctor Status</label>
                    <select wire:model.live="filterStatus" class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-1.5 px-3">
                        <option value="all">All Doctors</option>
                        <option value="unvisited">🚨 Unvisited (0 Visits)</option>
                        <option value="behind">⚠️ Behind Frequency</option>
                        <option value="completed">✅ Target Met</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="exportCsv" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV / Excel
                </button>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Doctor / Clinic</th>
                            <th class="px-4 py-3">Region / City</th>
                            <th class="px-4 py-3">Specialty</th>
                            <th class="px-4 py-3">Class</th>
                            <th class="px-4 py-3">Req. Freq</th>
                            <th class="px-4 py-3">Assigned Rep</th>
                            <th class="px-4 py-3 text-center">Visits Done</th>
                            <th class="px-4 py-3 text-center">Target Pts</th>
                            <th class="px-4 py-3 text-center">Achieved Pts</th>
                            <th class="px-4 py-3 text-center">Compliance %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->report_data as $row)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-4 py-3 font-mono font-bold text-xs text-gray-700 dark:text-gray-300">{{ $row['contact_code'] }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['contact_name'] }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $row['region_city'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $row['specialty'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                        {{ in_array($row['class'], ['A+', 'VIP']) ? 'bg-red-100 text-red-800' : ($row['class'] === 'A' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ $row['class'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $row['required_frequency'] }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-medium">{{ $row['assigned_user'] }}</td>
                                <td class="px-4 py-3 text-center font-bold {{ $row['visits_done'] >= $row['required_frequency'] ? 'text-emerald-600' : ($row['visits_done'] > 0 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $row['visits_done'] }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ $row['target_points'] }}</td>
                                <td class="px-4 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400">{{ $row['achieved_points'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                            <div class="h-2 rounded-full {{ $row['visit_compliance_pct'] >= 100 ? 'bg-emerald-500' : ($row['visit_compliance_pct'] > 0 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min(100, $row['visit_compliance_pct']) }}%"></div>
                                        </div>
                                        <span class="font-bold text-xs">{{ $row['visit_compliance_pct'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                    No doctor records match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
