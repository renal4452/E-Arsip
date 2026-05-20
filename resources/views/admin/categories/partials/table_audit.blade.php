<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Nama Tipe Dokumen</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase text-center">Status</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase text-center">Jumlah Dokumen</th>
                    <th class="text-end pe-4 py-3 text-muted small fw-bold text-uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditTypes as $type)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark">{{ $type->name_types }}</span>
                        <div class="text-muted small">{{ $type->description ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $type->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-3 py-2 rounded-pill small">
                            {{ $type->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </td>
                    <td class="text-center small fw-medium">{{ $type->documents_count }} Berkas</td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <form action="{{ route('categories.toggle', $type->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="module" value="audit">
                                <button type="submit" class="btn btn-sm {{ $type->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-3 px-3">
                                    {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('categories.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus permanen kategori ini?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="module" value="audit">
                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted small">Belum ada kategori audit.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>