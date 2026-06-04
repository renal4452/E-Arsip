@extends('layouts.app')

@section('content')
  <div class="p-4 sm:p-6 lg:p-8">
    {{-- PAGE HEADER --}}
    <div class="page-header flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="page-title text-2xl font-bold text-slate-900 tracking-tight">Inspektorat Dashboard</h1>
        <p class="page-subtitle text-sm text-slate-500 mt-1 flex items-center gap-2">
          <i class="bi bi-speedometer2"></i>
          Real-time monitoring & analytics sistem inspeksi dokumen
        </p>
      </div>

      <div class="flex items-center gap-4">
        <div class="card-base bg-white border border-slate-200 rounded-lg p-2.5 flex items-center gap-3 shadow-sm">
          <div class="text-xs font-semibold text-slate-500">
            Update:
            <span class="text-slate-900 ml-1">{{ now()->translatedFormat('d M Y H:i') }}</span>
          </div>
          <div class="h-4 w-px bg-slate-200"></div>

          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-100">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
            </span>
            Sistem Live
          </span>
        </div>
      </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
      <div class="flash flash-success mb-6 flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800" role="alert">
        <div class="flash-icon mt-0.5">
          <i class="bi bi-check2-circle text-lg"></i>
        </div>
        <div class="flash-msg">
          <div class="font-bold text-sm">Berhasil</div>
          <div class="text-sm mt-0.5 text-emerald-700">{{ session('success') }}</div>
        </div>
      </div>
    @endif

    {{-- SECTION 1: KEY METRICS (Telah Diperbaiki: Layout & Keseimbangan Visual) --}}
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-graph-up text-blue-600"></i>
        <h3 class="text-base font-bold text-slate-800">Metrik Utama</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        
        {{-- Total Dokumen --}}
        <a href="{{ route('documents.index') }}" class="card-stat group bg-white border border-slate-200 p-5 rounded-xl hover:shadow-lg transition-all duration-300 relative overflow-hidden">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Dokumen</div>
              <div class="text-3xl font-black text-slate-800">{{ $stats['total'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
              <i class="bi bi-file-earmark-text-fill"></i>
            </div>
          </div>
          <div class="mt-4 inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-md px-2 py-1">
            <i class="bi bi-arrow-up-short text-sm"></i>
            +{{ round(($stats['total'] / max($stats['total'], 1)) * 100) }}% bulan ini
          </div>
        </a>

        {{-- Menunggu ACC --}}
        <a href="{{ route('documents.index', ['status' => 'pending']) }}" class="card-stat group bg-white border border-slate-200 p-5 rounded-xl hover:shadow-lg transition-all duration-300 relative overflow-hidden">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Menunggu ACC</div>
              <div class="text-3xl font-black text-slate-800">{{ $stats['pending'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
              <i class="bi bi-hourglass-split"></i>
            </div>
          </div>
          <div class="mt-4 inline-flex items-center gap-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 rounded-md px-2 py-1">
            {{ round(($stats['pending'] / max($stats['total'], 1)) * 100) }}% dari total
          </div>
        </a>

        {{-- Perlu Revisi --}}
        <a href="{{ route('documents.index', ['status' => 'revisi']) }}" class="card-stat group bg-white border border-slate-200 p-5 rounded-xl hover:shadow-lg transition-all duration-300 relative overflow-hidden">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Perlu Revisi</div>
              <div class="text-3xl font-black text-slate-800">{{ $stats['revisi'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-colors">
              <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
          </div>
          <div class="mt-4 inline-flex items-center gap-1 text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 rounded-md px-2 py-1">
            Perbaikan aktif
          </div>
        </a>

        {{-- Disetujui --}}
        <a href="{{ route('documents.index', ['status' => 'approved']) }}" class="card-stat group bg-white border border-slate-200 p-5 rounded-xl hover:shadow-lg transition-all duration-300 relative overflow-hidden">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Disetujui</div>
              <div class="text-3xl font-black text-slate-800">{{ $stats['approved'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
              <i class="bi bi-shield-check"></i>
            </div>
          </div>
          <div class="mt-4 inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-md px-2 py-1">
            <i class="bi bi-check2-all"></i> Tersimpan aman
          </div>
        </a>

      </div>
    </div>

    {{-- SECTION 2: STATUS & RECENT DOCS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      
      {{-- Status Distribution (Telah Diperbaiki: Bar lebih tipis dan rapi) --}}
      <div class="section-wrapper bg-white border border-slate-200 rounded-xl overflow-hidden lg:col-span-1">
        <div class="section-header px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <h6 class="font-bold text-slate-800 text-sm">Distribusi Status</h6>
          <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-md">Total: {{ $stats['total'] }}</span>
        </div>

        <div class="p-6 flex flex-col gap-5">
          
          {{-- Menunggu --}}
          <div>
            <div class="flex justify-between items-center mb-1.5">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="text-sm font-medium text-slate-700">Menunggu ACC</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ $stats['pending'] }}</span>
            </div>
            <div class="progress-track w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="progress-fill h-full bg-amber-500 rounded-full transition-all duration-500" style="width: {{ round(($stats['pending'] / max($stats['total'], 1)) * 100) }}%;"></div>
            </div>
          </div>

          {{-- Revisi --}}
          <div>
            <div class="flex justify-between items-center mb-1.5">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="text-sm font-medium text-slate-700">Perlu Revisi</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ $stats['revisi'] }}</span>
            </div>
            <div class="progress-track w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="progress-fill h-full bg-rose-500 rounded-full transition-all duration-500" style="width: {{ round(($stats['revisi'] / max($stats['total'], 1)) * 100) }}%;"></div>
            </div>
          </div>

          {{-- Disetujui --}}
          <div>
            <div class="flex justify-between items-center mb-1.5">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-sm font-medium text-slate-700">Disetujui</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ $stats['approved'] }}</span>
            </div>
            <div class="progress-track w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="progress-fill h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ round(($stats['approved'] / max($stats['total'], 1)) * 100) }}%;"></div>
            </div>
          </div>

          <hr class="border-slate-100 my-2">

          {{-- Tingkat Penyelesaian --}}
          <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-100">
            <div class="flex justify-between items-center mb-2">
              <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wide">Tingkat Penyelesaian</span>
              <span class="text-lg font-black text-blue-600">{{ round(($stats['approved'] / max($stats['total'], 1)) * 100) }}%</span>
            </div>
            <div class="progress-track w-full h-1.5 bg-blue-100 rounded-full overflow-hidden">
              <div class="progress-fill h-full bg-blue-600 rounded-full transition-all duration-500" style="width: {{ round(($stats['approved'] / max($stats['total'], 1)) * 100) }}%;"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Recent Documents (Telah Diperbaiki: Kontras teks & Alignment vertikal) --}}
      <div class="section-wrapper bg-white border border-slate-200 rounded-xl overflow-hidden lg:col-span-2 flex flex-col">
        <div class="section-header px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h6 class="font-bold text-slate-800 text-sm">Aktivitas Dokumen Terbaru</h6>
          <a href="{{ route('documents.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
            Lihat Semua <i class="bi bi-arrow-right"></i>
          </a>
        </div>

        <div class="flex-1 overflow-hidden">
          <div class="overflow-y-auto max-h-[400px] custom-scrollbar">
            <div class="flex flex-col">
              @forelse($recentDocs as $doc)
                <a href="{{ route('documents.show', $doc->id) }}" class="group px-6 py-4 border-b border-slate-100 hover:bg-slate-50 transition-colors flex items-center justify-between gap-4">
                  
                  <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                      <i class="bi bi-file-earmark-richtext text-lg"></i>
                    </div>

                    <div class="min-w-0">
                      <p class="text-sm font-bold text-slate-800 truncate group-hover:text-blue-600 transition-colors">
                        {{ $doc->title }}
                      </p>
                      {{-- Kontras teks ditingkatkan ke text-slate-500 --}}
                      <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">
                        {{ $doc->division->name ?? '-' }} <span class="mx-1.5 text-slate-300">•</span> {{ $doc->docType->name ?? 'Dokumen' }}
                      </p>
                    </div>
                  </div>

                  {{-- Memastikan Badge dan Waktu Rata Tengah secara Vertikal --}}
                  <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 shrink-0 text-right">
                    @php
                      $badgeClass = match($doc->status) {
                        'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
                        'revisi'   => 'bg-rose-50 text-rose-700 border-rose-200',
                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        default    => 'bg-slate-100 text-slate-600 border-slate-200',
                      };
                      $badgeLabel = match($doc->status) {
                        'pending'  => 'Menunggu',
                        'revisi'   => 'Revisi',
                        'approved' => 'Disetujui',
                        default    => 'Draft',
                      };
                    @endphp

                    <span class="inline-flex items-center justify-center px-2 py-0.5 border rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                      {{ $badgeLabel }}
                    </span>
                    {{-- Waktu digelapkan sedikit --}}
                    <span class="text-[11px] font-medium text-slate-500 sm:w-20 sm:text-right whitespace-nowrap">
                      {{ $doc->created_at->diffForHumans() }}
                    </span>
                  </div>
                </a>
              @empty
                <div class="p-8 text-center flex flex-col items-center justify-center">
                  <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-3">
                    <i class="bi bi-inbox text-2xl"></i>
                  </div>
                  <div class="text-sm font-bold text-slate-700">Belum ada dokumen</div>
                  <div class="text-xs text-slate-500 mt-1">Dokumen terbaru akan muncul di sini setelah diunggah.</div>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 3: QUICK ACTIONS --}}
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-4">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi Cepat</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('documents.create') }}" class="card-base bg-white border border-slate-200 rounded-xl p-4 hover:shadow-md hover:border-blue-300 transition-all flex items-center gap-4 group">
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-105 transition-transform">
            <i class="bi bi-cloud-arrow-up-fill"></i>
          </div>
          <div>
            <h6 class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors">Unggah Dokumen</h6>
            <p class="text-xs text-slate-500 mt-0.5">Mulai inspeksi baru</p>
          </div>
        </a>

        <a href="{{ route('documents.index') }}" class="card-base bg-white border border-slate-200 rounded-xl p-4 hover:shadow-md hover:border-indigo-300 transition-all flex items-center gap-4 group">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-105 transition-transform">
            <i class="bi bi-folder2-open"></i>
          </div>
          <div>
            <h6 class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">Arsip Lengkap</h6>
            <p class="text-xs text-slate-500 mt-0.5">Kelola & filter data</p>
          </div>
        </a>

        <div class="card-base bg-slate-50 border border-slate-200 rounded-xl p-4 opacity-70 cursor-not-allowed relative overflow-hidden flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-400 flex items-center justify-center text-xl">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
          <div>
            <h6 class="font-bold text-slate-600 text-sm">Laporan Audit</h6>
            <p class="text-xs text-slate-400 mt-0.5">Fitur segera hadir</p>
          </div>
          <div class="absolute top-3 right-3 text-slate-300">
            <i class="bi bi-lock-fill text-xs"></i>
          </div>
        </div>

        <div class="card-base bg-slate-50 border border-slate-200 rounded-xl p-4 opacity-70 cursor-not-allowed relative overflow-hidden flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-400 flex items-center justify-center text-xl">
            <i class="bi bi-gear-fill"></i>
          </div>
          <div>
            <h6 class="font-bold text-slate-600 text-sm">Pengaturan</h6>
            <p class="text-xs text-slate-400 mt-0.5">Akses administrator</p>
          </div>
          <div class="absolute top-3 right-3 text-slate-300">
            <i class="bi bi-lock-fill text-xs"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection