import React, { useEffect } from 'react';
import { useForm } from '@inertiajs/react';

export default function DocumentModals({ modal, closeModal }) {
    // Satu form untuk menangani semua jenis upload dokumen
    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        file: null,
    });

    // Reset form setiap kali modal tertutup
    useEffect(() => {
        if (!modal.isOpen) {
            reset();
            clearErrors();
        }
    }, [modal.isOpen]);

    if (!modal.isOpen || !modal.data) return null;

    const doc = modal.data;
    const isTTE = modal.type === 'TTE';

    // Konfigurasi dinamis berdasarkan tipe modal
    const config = isTTE ? {
        title: 'Unggah Berkas Final (TTE)',
        icon: <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>,
        bgColor: 'bg-purple-50',
        borderColor: 'border-purple-200',
        btnClass: 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
        url: `/documents/${doc.id}/upload-final`,
        description: 'Gunakan fitur ini untuk melampirkan berkas PDF yang telah dibubuhi Tanda Tangan Elektronik (TTE).',
        accept: '.pdf',
    } : {
        title: 'Unggah Ulang Berkas',
        icon: <svg className="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>,
        bgColor: 'bg-orange-50',
        borderColor: 'border-orange-200',
        btnClass: 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500',
        url: `/documents/${doc.id}/force-update`,
        description: 'Tindakan ini akan menaikkan versi dokumen dan mengembalikan status menjadi Menunggu ACC.',
        accept: '.pdf,.doc,.docx,.xls,.xlsx',
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(config.url, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    };

    return (
        <div className="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {/* Backdrop dengan efek blur */}
            <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div className="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" aria-hidden="true" onClick={closeModal}></div>

                <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {/* Modal Panel */}
                <div className="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <form onSubmit={handleSubmit}>
                        
                        {/* Header Modal */}
                        <div className={`${config.bgColor} px-6 py-4 border-b ${config.borderColor} flex items-center gap-3`}>
                            {config.icon}
                            <h3 className="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                {config.title}
                            </h3>
                        </div>

                        {/* Body Modal */}
                        <div className="bg-white px-6 pt-5 pb-6">
                            <div className="mb-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p className="mb-1">Target Dokumen: <strong className="text-gray-900">{doc.no_doc || doc.title}</strong></p>
                                <p className="text-gray-500 text-xs">{config.description}</p>
                            </div>

                            <div>
                                <label className="block text-sm font-bold text-gray-700 mb-2">Pilih Berkas Baru</label>
                                <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-500 transition-colors bg-gray-50">
                                    <div className="space-y-1 text-center">
                                        <svg className="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                        <div className="flex text-sm text-gray-600 justify-center">
                                            <label className="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload a file</span>
                                                <input 
                                                    type="file" 
                                                    className="sr-only" 
                                                    accept={config.accept}
                                                    required
                                                    onChange={e => setData('file', e.target.files[0])}
                                                />
                                            </label>
                                        </div>
                                        <p className="text-xs text-gray-500">
                                            {data.file ? <span className="font-bold text-blue-600">{data.file.name}</span> : `Format: ${config.accept} (Max 10MB)`}
                                        </p>
                                    </div>
                                </div>
                                {errors.file && <p className="mt-2 text-sm text-red-600 font-medium">{errors.file}</p>}
                            </div>
                        </div>

                        {/* Footer Modal */}
                        <div className="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                            <button 
                                type="button" 
                                onClick={closeModal}
                                disabled={processing}
                                className="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                disabled={processing || !data.file}
                                className={`w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto sm:text-sm transition-colors disabled:opacity-50 ${config.btnClass}`}
                            >
                                {processing ? 'Memproses...' : 'Mulai Unggah'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}