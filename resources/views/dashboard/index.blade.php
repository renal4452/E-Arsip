@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Panel Analitik Inspektorat</h3>
            <p class="text-sm text-slate-500 flex items-center gap-2 font-medium">
                <i class="bi bi-speedometer2 text-indigo-500 text-lg"></i> 
                Pantau statistik dokumen dan alur kerja secara real-time.
            </p>
        </div>
        <div class="hidden md:block text-right">
            <div class="font-bold text-slate-800 text-sm mb-2">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> Sistem Online
            </span>
        </div>
    </div>

    @php
        // TWEAK TAILWIND: Class warna dideklarasikan penuh agar tidak di-purge (dihapus) oleh compiler Tailwind.
        $cards = [
            ['label' => 'Menunggu ACC', 'value' => $stats['pending'] ?? 0, 'icon' => 'bi-hourglass-split', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'link' => route('documents.index', ['status' => 'pending'])],
            ['label' => 'Perlu Revisi', 'value' => $stats['revisi'] ?? 0, 'icon' => 'bi-pencil-square', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'link' => route('documents.index', ['status' => 'revisi'])],
            ['label' => 'Total Arsip', 'value' => $stats['approved'] ?? 0, 'icon' => 'bi-archive-fill', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'link' => route('documents.index', ['status' => 'approved'])],
            ['label' => 'Ruang Berbagi', 'value' => $totalShared ?? 0, 'icon' => 'bi-share-fill', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'link' => route('shared_documents.index')],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($cards as $card)
        <a href="{{ $card['link'] }}" class="block group">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg h-full">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl {{ $card['bg'] }} {{ $card['text'] }} flex items-center justify-center shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi {{ $card['icon'] }} text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
                        <h3 class="text-2xl font-black text-slate-800 leading-none">{{ number_format($card['value'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col h-full">
            <div class="px-6 py-5 border-b border-slate-100">
                <h6 class="font-extrabold text-slate-800 m-0">Tren Volume LHP (6 Bulan Terakhir)</h6>
            </div>
            <div class="p-6 flex-1 relative min-h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col h-full">
            <div class="px-6 py-5 border-b border-slate-100 text-center">
                <h6 class="font-extrabold text-slate-800 m-0">Komposisi Status</h6>
            </div>
            <div class="p-6 flex flex-col items-center justify-center flex-1">
                <div class="relative w-full max-w-[240px] aspect-square">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-6 w-full border-t border-slate-100 pt-4 text-center">
                    <p class="text-sm text-slate-500 font-medium">Total dokumen diproses: <strong class="text-slate-800">{{ array_sum($stats) }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
            <h6 class="font-extrabold text-slate-800 m-0">Log Aktivitas Terbaru</h6>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold border border-slate-200">Live Update</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4 w-64">Pengguna</th>
                        <th class="px-6 py-4">Aktivitas</th>
                        <th class="px-6 py-4 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentActivities ?? [] as $log)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-slate-700 text-sm group-hover:text-indigo-600 transition-colors">{{ $log->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <span class="font-bold text-slate-800">{{ $log->action }}</span> &mdash; {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 text-right text-xs font-medium text-slate-400 whitespace-nowrap">
                            {{ $log->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-300 mb-3">
                                <i class="bi bi-inbox-fill text-2xl"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-500">Belum ada catatan aktivitas sistem.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil Data dari Laravel
        const trendData = @json($monthlyData ?? ['labels' => [], 'values' => []]);
        const statsData = @json($stats ?? ['pending' => 0, 'revisi' => 0, 'approved' => 0]);

        // Palet Warna Tailwind
        const twIndigo = '#6366f1';
        const twIndigoLight = 'rgba(99, 102, 241, 0.1)';
        const twAmber = '#f59e0b';
        const twRose = '#f43f5e';
        const twEmerald = '#10b981';

        // 1. Grafik Tren (Line Chart)
        const ctxTrend = document.getElementById('trendChart');
        if(ctxTrend) {
            new Chart(ctxTrend.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendData.labels,
                    datasets: [{
                        label: 'Volume Berkas',
                        data: trendData.values,
                        borderColor: twIndigo,
                        backgroundColor: twIndigoLight,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: twIndigo,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'Inter' } },
                            border: { display: false },
                            grid: { color: '#f1f5f9' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { family: 'Inter' } },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        // 2. Grafik Komposisi (Doughnut Chart)
        const ctxStatus = document.getElementById('statusChart');
        if(ctxStatus) {
            new Chart(ctxStatus.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu ACC', 'Perlu Revisi', 'Telah Disetujui'],
                    datasets: [{
                        data: [statsData.pending, statsData.revisi, statsData.approved],
                        backgroundColor: [twAmber, twRose, twEmerald],
                        hoverOffset: 4,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                usePointStyle: true, 
                                padding: 20, 
                                font: { family: 'Inter', size: 12 },
                                color: '#475569'
                            } 
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection