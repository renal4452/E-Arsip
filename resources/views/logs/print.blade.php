<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Log_Aktivitas_Sistem_{{ date('Ymd') }}</title>
    
    @vite(['resources/css/app.css'])
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* 🎨 STYLING KHUSUS UNTUK LAYAR MONITOR */
        @media screen {
            body { background-color: #f1f5f9; padding: 3rem 0; font-family: 'Times New Roman', Times, serif; }
            .paper { 
                background: white; 
                width: 210mm; /* Lebar Kertas A4 */
                min-height: 297mm; /* Tinggi Kertas A4 */
                margin: auto; 
                padding: 20mm; 
                box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            }
        }

        /* 🖨️ STYLING KHUSUS UNTUK MESIN CETAK / PDF */
        @media print {
            @page { size: A4 portrait; margin: 1.5cm; }
            body { background-color: white; font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: black; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .paper { width: 100%; padding: 0; margin: 0; box-shadow: none; border: none; }
            .no-print { display: none !important; }
            
            /* Mencegah tabel terpotong jelek di tengah halaman */
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; } 
        }

        /* 📏 ATURAN TABEL UNIVERSAL (Layar & Cetak) */
        .print-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .print-table th, .print-table td { 
            border: 1px solid black !important; 
            padding: 8px 10px; 
            vertical-align: top; 
        }
        .print-table th { 
            background-color: #e2e8f0 !important; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-align: center;
            vertical-align: middle;
            font-size: 10pt;
        }
        .print-table td { font-size: 10pt; }
    </style>
</head>
<body class="text-slate-900">

    <div class="no-print max-w-[210mm] mx-auto mb-6 text-center">
        <div class="inline-flex shadow-sm rounded-lg overflow-hidden">
            <a href="{{ route('logs.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Log
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 border border-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <i class="bi bi-printer"></i> Cetak Dokumen Resmi
            </button>
        </div>
        <p class="text-slate-500 text-xs mt-3 italic font-sans">Format: Kertas A4, Orientasi Potret (Portrait)</p>
    </div>

    <div class="paper">
        
        <div class="text-center border-b-[3px] border-black pb-4 mb-6">
            <h3 class="font-bold text-xl mb-1 uppercase">INSPEKTORAT DAERAH</h3>
            <h5 class="font-bold text-lg mb-2 uppercase">PROVINSI / KABUPATEN / KOTA XXX</h5>
            <p class="font-bold uppercase text-sm m-0">Laporan Rekam Jejak Aktivitas (Audit Trail) Sistem Terpadu</p>
            <div class="flex justify-between mt-4 text-xs font-sans">
                <span>Waktu Cetak: {{ date('d/m/Y H:i:s') }} WIB</span>
                <span>Dicetak Oleh: {{ auth()->user()->name ?? 'Administrator' }}</span>
            </div>
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Waktu Kejadian</th>
                    <th style="width: 20%;">Aktor / Pengguna</th>
                    <th style="width: 15%;">Aksi</th>
                    <th style="width: 30%;">Keterangan Aktivitas</th>
                    <th style="width: 15%;">Jejak Forensik</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    
                    <td class="text-center">
                        {{ $log->created_at->format('d/m/Y') }}<br>
                        <strong>{{ $log->created_at->format('H:i:s') }} WIB</strong>
                    </td>
                    
                    <td>
                        <div class="font-bold">{{ $log->user->name ?? 'Sistem' }}</div>
                        <div class="text-xs italic mt-1">Akses: {{ $log->user->role->name ?? 'User Sistem' }}</div>
                        <div class="text-xs italic">Unit: {{ $log->user->division->name ?? '-' }}</div>
                    </td>
                    
                    <td class="text-center font-bold text-xs">
                        {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                    </td>
                    
                    <td style="line-height: 1.5;">
                        <span class="font-bold block mb-1">{{ $log->description }}</span>
                        
                        @if($log->document)
                            <div class="mt-2 pt-2 border-t border-dashed border-gray-400 text-[10px]">
                                <table style="width: 100%; border: none !important; margin: 0;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; padding: 1px 0; width: 60px;">No. Surat</td>
                                        <td style="border: none !important; padding: 1px 0;" class="font-bold">: {{ $log->document->no_doc }}</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; padding: 1px 0;">ID Data</td>
                                        <td style="border: none !important; padding: 1px 0;">: #{{ str_pad($log->document_id, 4, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </td>
                    
                    <td class="text-center text-xs">
                        IP: <code class="font-sans bg-gray-100 px-1">{{ $log->ip_address }}</code><br>
                        <span class="text-[10px] block mt-1">Log ID: #{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 font-bold">Data rekam jejak (audit trail) tidak ditemukan dalam periode pencarian ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex justify-end mt-16 text-sm">
            <div class="text-center w-64">
                <p class="mb-1">Palangka Raya, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="mb-16">Mengetahui,<br>Administrator Sistem / Pengawas</p>
                
                <p class="font-bold mb-0 underline">{{ auth()->user()->name ?? '....................................' }}</p>
                <p class="text-xs mt-1">NIP. ....................................</p>
            </div>
        </div>

    </div>

    <script>
        // Opsional: Langsung buka dialog print saat halaman dimuat
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>