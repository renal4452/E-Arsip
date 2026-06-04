<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Nama Tipe Dokumen</th>
                    <th class="px-6 py-4 text-center w-36 whitespace-nowrap">Status</th>
                    <th class="px-6 py-4 text-center w-44 whitespace-nowrap">Jumlah Dokumen</th>
                    <th class="px-6 py-4 text-center w-40 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($auditTypes as $type)
                    <tr class="hover:bg-slate-50/80 transition-colors duration-200 group">
                        
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 text-sm">{{ $type->name_types }}</div>
                            <div class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $type->description }}">{{ $type->description ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($type->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> AKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> NONAKTIF
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-semibold text-slate-600">
                            {{ $type->documents_count ?? 0 }} Berkas
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('categories.toggle', $type->id) }}" method="POST" class="m-0">
                                    @csrf 
                                    @method('PATCH')
                                    <input type="hidden" name="module" value="audit">
                                    @if($type->is_active)
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 rounded-lg hover:bg-amber-600 hover:text-white hover:border-amber-600 transition-all shadow-sm">
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-sm">
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>

                                <form action="{{ route('categories.destroy', $type->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus permanen kategori ini?')">
                                    @csrf 
                                    @method('DELETE')
                                    <input type="hidden" name="module" value="audit">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-rose-600 hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm" title="Hapus Kategori">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm font-medium text-slate-400 bg-slate-50/30">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="bi bi-folder-x text-3xl text-slate-300"></i>
                                <span class="font-semibold text-slate-500">Belum ada kategori audit.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>