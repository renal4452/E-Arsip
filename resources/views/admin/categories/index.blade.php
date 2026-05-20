@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Kategori</h3>
            <p class="text-muted small mb-0">Kelola klasifikasi untuk draf Audit dan Dokumen Publik.</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </button>
    </div>

    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm d-inline-flex" id="categoryTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 px-4" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit-pane" type="button" role="tab">
                <i class="bi bi-clipboard-check me-2"></i>Kategori Audit (Pre-LHP)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-4" id="shared-tab" data-bs-toggle="tab" data-bs-target="#shared-pane" type="button" role="tab">
                <i class="bi bi-share me-2"></i>Kategori Ruang Berbagi
            </button>
        </li>
    </ul>

    <div class="tab-content" id="categoryTabContent">
        <div class="tab-pane fade show active" id="audit-pane" role="tabpanel" tabindex="0">
            @include('admin.categories.partials.table_audit', ['types' => $auditTypes])
        </div>

        <div class="tab-pane fade" id="shared-pane" role="tabpanel" tabindex="0">
            @include('admin.categories.partials.table_shared', ['types' => $sharedTypes])
        </div>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-3">Tambah Kategori Baru</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Peruntukan Modul</label>
                        <select name="module" class="form-select rounded-3" required>
                            <option value="audit">Manajemen Dokumen (Audit)</option>
                            <option value="shared">Ruang Berbagi (Publik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: SOP atau LHP Khusus" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Jelaskan kegunaan kategori ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection