<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocType;
use App\Models\SharedType;
use App\Models\Document;
use App\Models\SharedDocument;
use App\Http\Requests\StoreCategoryRequest;

// ❌ HAPUS: use App\Traits\ApiResponse;

class CategoryManagerController extends Controller
{

    public function index()
    {
        $auditTypes = DocType::withCount('documents')->get();
        $sharedTypes = SharedType::withCount('sharedDocuments')->get();

        // Langsung lempar ke view Blade
        return view('admin.categories.index', compact('auditTypes', 'sharedTypes'));
    }

    public function store(StoreCategoryRequest $request)
    {
        // Validasi dan pengecekan Unique sudah diurus sepenuhnya oleh StoreCategoryRequest!
        
        if ($request->module === 'audit') {
            DocType::create([
                'name_types' => $request->name,
                'description' => $request->description,
                'is_active' => true
            ]);
        } else {
            SharedType::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true
            ]);
        }

        // Redirect dengan Flash Message session
        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $type = $request->module === 'audit' 
            ? DocType::findOrFail($id) 
            : SharedType::findOrFail($id);

        $type->update(['is_active' => !$type->is_active]);

        return back()->with('success', 'Status kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        if ($request->module === 'audit') {
            $type = DocType::findOrFail($id);
            // Cek relasi sebelum hapus
            if (Document::where('doc_type_id', $id)->exists()) {
                return back()->with('error', 'Kategori audit tidak bisa dihapus karena memiliki dokumen terkait.');
            }
        } else {
            $type = SharedType::findOrFail($id);
            // Cek relasi sebelum hapus
            if (SharedDocument::where('category_id', $id)->exists()) {
                return back()->with('error', 'Kategori shared tidak bisa dihapus karena memiliki dokumen terkait.');
            }
        }

        $type->delete();

        return back()->with('success', 'Kategori berhasil dihapus permanen.');
    }
}