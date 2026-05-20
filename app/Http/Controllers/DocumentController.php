<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocType;
use App\Services\DocumentService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

// Form Requests
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateRevisionRequest;
use App\Http\Requests\ReviewDocumentRequest;
use App\Http\Requests\UploadFinalRequest;
use App\Http\Requests\ForceUpdateRequest;

// ❌ HAPUS: use App\Traits\ApiResponse;
// ❌ HAPUS: use App\Http\Resources\DocumentResource;
// ❌ HAPUS: use Inertia\Inertia;

class DocumentController extends Controller
{
    protected $docService;
    protected $workflowService;

    public function __construct(DocumentService $docService, WorkflowService $workflowService)
    {
        $this->docService = $docService;
        $this->workflowService = $workflowService;
    }

    public function index(Request $request): View
    {
        // Tangkap semua input filter dari Blade
        $filters = $request->only(['search', 'status', 'year', 'type', 'start_date', 'end_date']);

        // Ambil data dengan Eager Loading
        $documents = Document::with(['docType', 'division', 'latestVersion'])
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
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Ambil data kategori untuk dropdown filter
        $categories = DocType::all();

        // Hitung statistik untuk Dashboard/Header Index
        $stats = [
            'pending' => Document::where('status', 'pending')->count(),
            'approved' => Document::where('status', 'approved')->count(),
        ];

        // ✅ UBAH: Render ke Blade
        return view('documents.index', compact('documents', 'categories', 'stats', 'filters'));
    }

    public function show(Document $document): View
    {
        // 1. Ambil relasi yang dibutuhkan untuk halaman detail
        $document->load(['docType', 'division', 'auditor']);

        // 2. Ambil riwayat versi dokumen (terbaru di atas)
        $versions = $document->versions()->with('uploader')->latest()->get();

        // 3. Ambil log aktivitas terkait dokumen ini
        $logs = $document->activityLogs()->with('user')->latest()->get();

        // ✅ UBAH: Render ke Blade
        return view('documents.show', compact('document', 'versions', 'logs'));
    }

    public function create(): View
    {
        $categories = DocType::all();
        return view('documents.create', compact('categories'));
    }
    
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->docService->createNewDocument($request->validated(), $request->user());

        // ✅ HAPUS: Pengecekan JSON. Langsung redirect!
        return redirect()->route('documents.index')->with('success', 'Draf berhasil diunggah.');
    }

    public function updateRevision(UpdateRevisionRequest $request, Document $document): RedirectResponse
    {
        $this->docService->uploadNewVersion($document, $request->file('file'), $request->user(), 'pending', null);
        
        return redirect()->route('documents.show', $document->id)->with('success', 'Revisi berhasil dikirim.');
    }

    public function approve(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('review', $document); 
        $this->workflowService->processApproval($document, $request->user());
        
        return back()->with('success', 'Dokumen berhasil disetujui.');
    }

    public function storeRevisi(ReviewDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('review', $document);
        $this->workflowService->processRejection($document, $request->note, $request->user());
        
        return redirect()->route('documents.show', $document->id)->with('success', 'Permintaan revisi dikirim.');
    }

    public function uploadFinalDocument(UploadFinalRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('review', $document);

        if (!$document->isApproved()) {
            // ✅ HAPUS: Pengecekan JSON
            return back()->with('error', 'Dokumen belum di-ACC.');
        }

        $this->docService->uploadFinalTTE($document, $request->file('file'), $request->user());

        return back()->with('success', 'Dokumen Final (TTE) berhasil diunggah.');
    }

    public function forceUpdateFile(ForceUpdateRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('forceUpdate', $document);

        $this->docService->uploadNewVersion($document, $request->file('file'), $request->user(), 'pending', 'Diperbarui mandiri.');

        return back()->with('success', 'Berkas berhasil diperbarui.');
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        if ($document->isLocked()) {
            // ✅ HAPUS: Pengecekan JSON
            return back()->with('error', '⚠️ Dokumen Terkunci.');
        }

        $this->docService->deleteDocument($document);

        return redirect()->route('documents.index')->with('success', 'Dokumen dihapus.');
    }
}