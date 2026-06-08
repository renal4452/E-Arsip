<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocType;
use App\Services\DocumentService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

// Form Requests
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateRevisionRequest;
use App\Http\Requests\ReviewDocumentRequest;
use App\Http\Requests\UploadFinalRequest;
use App\Http\Requests\ForceUpdateRequest;

class DocumentController extends Controller
{
    protected $docService;
    protected $workflowService;

    public function __construct(DocumentService $docService, WorkflowService $workflowService)
    {
        $this->docService = $docService;
        $this->workflowService = $workflowService;
    }

    /**
     * Menampilkan daftar dokumen dengan filter (Pengecualian untuk Superadmin & Inspektur).
     */
    public function index(Request $request): View
    {
        
        $filters = $request->only(['search', 'status', 'year', 'type', 'start_date', 'end_date']);
        $viewMode = $request->get('view', 'folder');
        
        $user = $request->user();

        $query = Document::with(['docType', 'division', 'latestVersion'])
            // PENGAMAN & PENGECUALIAN:
            // Jika BUKAN Superadmin (1) DAN BUKAN Inspektur (3), saring berdasarkan divisi
            ->when(!in_array($user->role_id, [1, 3]), function ($query) use ($user) {
                return $query->where('division_id', $user->division_id);
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_doc', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('doc_type_id', $type);
            })
            ->when($filters['year'] ?? null, function ($query, $year) {
                $query->whereYear('created_at', $year);
            });

        if ($viewMode === 'folder') {
            $documents = $query->latest()->get();
        } else {
            $documents = $query->latest()->paginate(10)->withQueryString();
        }

        $categories = DocType::all();

        // Statistik juga menyesuaikan hak akses role_id
        $statsQueryPending = Document::where('status', 'pending');
        $statsQueryApproved = Document::where('status', 'approved');

        if (!in_array($user->role_id, [1, 3])) {
            $statsQueryPending->where('division_id', $user->division_id);
            $statsQueryApproved->where('division_id', $user->division_id);
        }

        $stats = [
            'pending' => $statsQueryPending->count(),
            'approved' => $statsQueryApproved->count(),
        ];

        return view('documents.index', compact('documents', 'categories', 'stats', 'filters', 'viewMode'));
    }

    /**
     * Menampilkan detail dokumen.
     */
    public function show(Document $document, Request $request): View
    {
        $user = $request->user();

        // Buka akses jika dia Superadmin (1) / Inspektur (3). Jika bukan, cek kesamaan divisi.
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        $document->load(['docType', 'division', 'auditor']);
        $versions = $document->versions()->with('uploader')->latest()->get();
        $logs = $document->activityLogs()->with('user')->latest()->get();

        return view('documents.show', compact('document', 'versions', 'logs'));
    }

    /**
     * Menampilkan form pembuatan draf dokumen baru.
     */
    public function create(): View
    {
        $categories = DocType::all();
        return view('documents.create', compact('categories'));
    }
    
    /**
     * Menyimpan draf dokumen baru ke sistem.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->docService->createNewDocument($request->validated(), $request->user());
        return redirect()->route('documents.index')->with('success', 'Draf berhasil diunggah.');
    }

    /**
     * Menampilkan form revisi dokumen.
     */
    public function revisiForm(Document $document, Request $request): View
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        Gate::authorize('review', $document);
        $document->load(['docType', 'division']);

        return view('documents.revisi', compact('document'));
    }

    /**
     * Memproses penolakan dokumen.
     */
    public function storeRevisi(ReviewDocumentRequest $request, Document $document): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        $this->workflowService->processRejection($document, $request->notes, $request->user());
        
        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Dokumen berhasil diubah statusnya menjadi Perlu Revisi.');
    }

    /**
     * Mengunggah file berkas draf perbaikan baru.
     */
    public function updateRevision(UpdateRevisionRequest $request, Document $document): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        $this->docService->uploadNewVersion($document, $request->file('file'), $request->user(), 'pending', null);
        return redirect()->route('documents.show', $document->id)->with('success', 'Revisi berhasil dikirim.');
    }

    /**
     * Menyetujui dokumen (ACC).
     */
    public function approve(Request $request, Document $document): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        Gate::authorize('review', $document); 
        $this->workflowService->processApproval($document, $request->user());
        
        return back()->with('success', 'Dokumen berhasil disetujui.');
    }

    /**
     * Mengunggah berkas final (TTE).
     */
    public function uploadFinalDocument(UploadFinalRequest $request, Document $document): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        Gate::authorize('review', $document); 

        if (!$document->isApproved()) {
            return back()->with('error', 'Dokumen belum di-ACC.');
        }

        $this->docService->uploadFinalTTE($document, $request->file('file'), $request->user());

        return back()->with('success', 'Dokumen Final (TTE) berhasil diunggah.');
    }

    /**
     * Memperbarui file berkas secara mandiri (Force Update).
     */
    public function forceUpdateFile(ForceUpdateRequest $request, Document $document): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        Gate::authorize('forceUpdate', $document); 

        $this->docService->uploadNewVersion($document, $request->file('file'), $request->user(), 'pending', 'Diperbarui mandiri.');

        return back()->with('success', 'Berkas berhasil diperbarui.');
    }

    /**
     * Mengunduh berkas fisik berdasarkan ID versi dokumen.
     */
    public function download(Request $request, $versionId)
    {
        $document = Document::whereHas('versions', function($q) use ($versionId) {
            $q->where('id', $versionId);
        })->firstOrFail();

        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh dokumen divisi ini.');
        }

        $version = $document->versions()->where('id', $versionId)->firstOrFail();
        
        $filePath = $version->file_path ?? $version->path ?? $version->file; 

        if (!$filePath) {
            return back()->with('error', 'Path berkas tidak ditemukan di database.');
        }

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        } elseif (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->download($filePath);
        }

        return back()->with('error', 'Berkas fisik tidak ditemukan di server.');
    }

    /**
     * Menghapus dokumen dari sistem.
     */
    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role_id, [1, 3]) && $document->division_id !== $user->division_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen divisi ini.');
        }

        Gate::authorize('delete', $document); 

        if ($document->isLocked()) {
            return back()->with('error', '⚠️ Dokumen Terkunci.');
        }

        $this->docService->deleteDocument($document);

        return redirect()->route('documents.index')->with('success', 'Dokumen dihapus.');
    }
}