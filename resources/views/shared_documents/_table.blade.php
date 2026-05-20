<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                <th class="px-6 py-4">Nama Dokumen</th>
                <th class="px-6 py-4">Kategori</th>
                <th class="px-6 py-4">Waktu</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($items as $doc)
                @php
                    $folderSlug = Str::slug($isAdminOrInspektur ? ($doc->division->name ?? 'Sekretariat') : ($doc->category->name ?? 'Umum / Lainnya'));
                @endphp
                <tr class="hover:bg-slate-50 transition-colors" x-show="activeFolder === null || activeFolder === '{{ $folderSlug }}'">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-file-earmark-text text-indigo-500 text-lg"></i>
                            <div>
                                <div class="font-bold text-sm text-slate-800">{{ $doc->title }}</div>
                                <div class="text-xs text-slate-400">{{ $doc->description ?? 'Tanpa deskripsi' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-slate-600">
                        {{ $doc->category->name ?? 'Umum' }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $doc->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('shared_documents.download', $doc->id) }}" 
                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg transition-colors shadow-sm">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>