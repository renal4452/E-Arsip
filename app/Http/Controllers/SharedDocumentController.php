<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\SharedDocument;
use App\Models\SharedType;
use App\Http\Requests\StoreSharedDocumentRequest;
use Illuminate\Support\Facades\Storage;

// ❌ HAPUS: use App\Traits\ApiResponse;

class SharedDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $categories = SharedType::where('is_active', true)->get();

        $query = SharedDocument::with(['category', 'division', 'user'])
            ->filter($request->only(['category_id', 'search', 'year', 'start_date', 'end_date']))
            ->latest();

        $documents = $request->view == 'table' ? $query->paginate(10) : $query->get();

        // ✅ UBAH: Langsung lempar ke view
        return view('shared_documents.index', compact('documents', 'categories'));
    }

    public function create(): View
    {
        $categories = SharedType::where('is_active', true)->get();
        
        return view('shared_documents.create', compact('categories'));
    }

    public function store(StoreSharedDocumentRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('shared_documents', $fileName);

        SharedDocument::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'file_path' => $filePath,
            'division_id' => $request->user()->division_id,
            'user_id' => $request->user()->id,
        ]);

        // ✅ UBAH: Langsung redirect dengan session flash
        return redirect()->route('shared_documents.index')->with('success', 'Dokumen berhasil dibagikan ke Ruang Berbagi!');
    }

    public function download(SharedDocument $sharedDocument)
    {
        if (!Storage::exists($sharedDocument->file_path)) {
            // ✅ UBAH: Langsung kembali dengan pesan error
            return redirect()->back()->with('error', 'Maaf, file fisik tidak ditemukan di server.');
        }

        $extension = pathinfo($sharedDocument->file_path, PATHINFO_EXTENSION);
        return Storage::download($sharedDocument->file_path, $sharedDocument->title . '.' . $extension);
    }

    public function destroy(SharedDocument $sharedDocument): RedirectResponse
    {
        $this->authorize('delete', $sharedDocument);

        if (Storage::exists($sharedDocument->file_path)) {
            Storage::delete($sharedDocument->file_path);
        }

        $sharedDocument->delete();

        // ✅ UBAH: Langsung redirect
        return redirect()->route('shared_documents.index')->with('success', 'Dokumen publik berhasil dihapus permanen.');
    }
}