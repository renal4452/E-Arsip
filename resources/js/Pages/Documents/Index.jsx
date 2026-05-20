import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import usePermissions from '/Hooks/usePermissions';
import StatusBadge from '../../Components/UI/StatusBadge';
import DataTable from '../../Components/Table/DataTable';
import InputField from '../../Components/Form/InputField';

export default function Index({ documents, filters }) {
    const { can } = usePermissions();
    
    // State untuk filter pencarian (diambil dari URL props jika ada)
    const [search, setSearch] = useState(filters?.search || '');

    // Effect untuk fitur "Debounce Search" (Ngetik selesai baru nge-request ke server)
    useEffect(() => {
        const delayDebounceFn = setTimeout(() => {
            // Hanya nge-hit backend kalau search berubah
            if (search !== filters?.search) {
                router.get('/documents', { search }, {
                    preserveState: true, // Biar state gak reset
                    preserveScroll: true, // Biar layar gak loncat ke atas
                    replace: true // Gak menuhin history browser
                });
            }
        }, 500); // Tunggu 500ms setelah user berhenti ngetik

        return () => clearTimeout(delayDebounceFn);
    }, [search]);

    return (
        <AuthenticatedLayout>
            <Head title="Manajemen LHP" />

            <div className="layout-container pt-6">
                
                {/* --- HEADER --- */}
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 className="text-2xl font-black text-slate-900 tracking-tight">Manajemen LHP</h2>
                        <p className="text-sm text-slate-500 mt-1">Kelola, tinjau, dan lacak seluruh dokumen hasil pemeriksaan.</p>
                    </div>

                    <div className="flex items-center gap-3 w-full sm:w-auto">
                        <div className="w-full sm:w-64">
                            <InputField 
                                id="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari nomor/judul dokumen..."
                            />
                        </div>
                        {can('upload_documents') && (
                            <Link href="/documents/create" className="btn-primary whitespace-nowrap h-[42px]">
                                Unggah Baru
                            </Link>
                        )}
                    </div>
                </div>

                {/* --- TABLE COMPONENT CALL --- */}
                <DataTable
                    columns={['No Dokumen', 'Perihal', 'Status', 'Aksi']}
                    data={documents.data}
                    paginationLinks={documents.links} // Kirim props pagination dari Laravel
                    emptyMessage="Pencarian tidak menemukan dokumen LHP."
                    renderRow={(doc) => (
                        <tr key={doc.id} className="hover:bg-slate-50 transition-colors">
                            <td className="px-6 py-4 text-slate-600 font-medium">
                                {doc.no_doc || '-'}
                            </td>
                            <td className="px-6 py-4">
                                <p className="font-bold text-slate-900">{doc.title}</p>
                                <p className="text-xs text-slate-500 mt-0.5">{doc.division?.name || 'Umum'}</p>
                            </td>
                            <td className="px-6 py-4 text-center">
                                <StatusBadge status={doc.status} />
                            </td>
                            <td className="px-6 py-4 text-right space-x-4">
                                <Link href={`/documents/${doc.id}`} className="text-indigo-600 font-bold hover:text-indigo-900">
                                    Detail
                                </Link>

                                {/* 🔥 INI DIA: Contextual Permission dari Backend 🔥 */}
                                {doc.permissions?.can_review && doc.status === 'pending' && (
                                    <Link href={`/documents/${doc.id}/review`} className="text-amber-600 font-bold hover:text-amber-900">
                                        Tinjau
                                    </Link>
                                )}
                            </td>
                        </tr>
                    )}
                />
            </div>
        </AuthenticatedLayout>
    );
}