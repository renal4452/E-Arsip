import { usePage } from '@inertiajs/react';

export default function usePermissions() {
    // Tarik data auth yang dikirim dari HandleInertiaRequests.php
    const { auth } = usePage().props;

    return {
        // Data User yang sedang login
        user: auth?.user,

        // Kumpulan permission dari backend (bentuknya object boolean)
        permissions: auth?.permissions || {},

        // Fungsi helper biar di UI tinggal panggil: can('upload_documents')
        can: (permissionName) => {
            // Cek apakah permission tersebut ada dan bernilai true
            return !!auth?.permissions?.[permissionName];
        },
    };
}