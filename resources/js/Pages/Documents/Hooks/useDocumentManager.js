import { useState, useEffect, useMemo } from 'react';
import { router, usePage } from '@inertiajs/react';

export default function useDocumentManager(initialFilters) {
    const { auth } = usePage().props;
    // Tambahkan tanda tanya di auth?.user
    const roleName = auth?.user?.role?.name || 'User';

    // 1. HELPER: Role Checker
    const hasRole = (...roles) => roles.includes(roleName);

    // 2. STATE: Filtering
    const [filters, setFilters] = useState({
        search: initialFilters?.search || '',
        status: initialFilters?.status || '',
        year: initialFilters?.year || '',
        type: initialFilters?.type || '',
    });

    // 3. STATE: Modals (Clean Pattern: 1 State untuk semua modal)
    const [modal, setModal] = useState({
        isOpen: false,
        type: null, // 'TTE', 'UPDATE', 'DELETE'
        data: null  // Menyimpan seluruh object document yang dipilih
    });

    // 4. ACTION: Modal Handlers
    const openModal = (type, documentData) => setModal({ isOpen: true, type, data: documentData });
    const closeModal = () => setModal({ isOpen: false, type: null, data: null });

    // 5. ACTION: Filter Handlers
    const updateFilter = (key, value) => setFilters(prev => ({ ...prev, [key]: value }));
    const resetFilters = () => setFilters({ search: '', status: '', year: '', type: '' });

    // 6. MAGIC DEBOUNCE (BUG FIXED: Menggunakan JSON.stringify)
    const filterKey = JSON.stringify(filters);

    useEffect(() => {
        const delay = setTimeout(() => {
            // Nembak ke backend dengan parameter yang bersih
            router.get('/documents', JSON.parse(filterKey), {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        }, 500);

        return () => clearTimeout(delay);
    }, [filterKey]); // 👈 Sekarang aman dari spam request!

    return {
        auth,
        hasRole,
        filters,
        updateFilter,
        resetFilters,
        modal,
        openModal,
        closeModal
    };
}