<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Target Visit Cycle</label>
                    <select wire:model.live="selectedCycleId" class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-1.5 px-3 font-semibold">
                        @foreach($this->cycles as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->status) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="recalculateSnapshots" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-lg shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Recalculate Snapshot Now
                </button>

                <button wire:click="exportCsv" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV Scorecard
                </button>
            </div>
        </div>

        {{-- Scorecards Grid / Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Medical Rep</th>
                            <th class="px-4 py-3 text-center">Assigned Doctors</th>
                            <th class="px-4 py-3 text-center">Coverage Rate %</th>
                            <th class="px-4 py-3 text-center">Planned vs Done</th>
                            <th class="px-4 py-3 text-center">Compliance %</th>
                            <th class="px-4 py-3 text-center">GPS Accuracy %</th>
                            <th class="px-4 py-3 text-center">Achieved / Target Pts</th>
                            <th class="px-4 py-3 text-center">Points %</th>
                            <th class="px-4 py-3 text-center">Unreported Days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->performance_data as $row)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $row['rep_name'] }}
                                    <div class="text-xs text-gray-400 font-normal">{{ $row['rep_email'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">{{ $row['total_assigned_contacts'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $row['coverage_rate_pct'] >= 80 ? 'bg-emerald-100 text-emerald-800' : ($row['coverage_rate_pct'] >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $row['coverage_rate_pct'] }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold font-mono">{{ $row['planned_vs_done'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $row['visit_compliance_pct'] >= 90 ? 'bg-emerald-100 text-emerald-800' : ($row['visit_compliance_pct'] >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $row['visit_compliance_pct'] }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $row['accuracy_pct'] >= 90 ? 'bg-indigo-100 text-indigo-800' : ($row['accuracy_pct'] >= 70 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $row['accuracy_pct'] }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400 font-mono">
                                    {{ $row['target_vs_achieved_points'] }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold">
                                    {{ $row['points_achieved_pct'] }}%
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $row['unreported_days_count'] == 0 ? 'bg-gray-100 text-gray-700' : ($row['unreported_days_count'] <= 2 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $row['unreported_days_count'] }} days
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    No performance data available for this cycle yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
