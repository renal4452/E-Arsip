<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notifikasi Dokumen</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 0;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        @php
            // Palet Warna Disesuaikan dengan Tailwind Aplikasi Kita
            $headerColor = '#4f46e5'; // Indigo-600 (Upload Baru / Update)
            if(isset($type)) {
                if($type == 'success') $headerColor = '#10b981'; // Emerald-500 untuk ACC
                if($type == 'danger' || $type == 'warning') $headerColor = '#f59e0b';  // Amber-500 untuk Revisi
            }
        @endphp
        <tr>
            <td style="background-color: {{ $headerColor }}; padding: 30px; text-align: center;">
                <h2 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; font-weight: 800;">E-Arsip Inspektorat</h2>
            </td>
        </tr>

        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 15px; color: #475569; margin-top: 0; line-height: 1.6;">
                    Halo,<br><br>
                    Terdapat pembaruan status pada dokumen di sistem E-Arsip. Berikut adalah rinciannya:
                </p>

                <div style="background-color: #f8fafc; border-left: 4px solid {{ $headerColor }}; padding: 15px 20px; border-radius: 4px; margin: 25px 0;">
                    <p style="margin: 0; font-size: 16px; color: #1e293b; font-weight: bold;">{{ $messageText }}</p>
                </div>

                <table width="100%" cellpadding="12" cellspacing="0" style="border-collapse: collapse; margin-bottom: 30px;">
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td width="35%" style="color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; padding-left: 0; letter-spacing: 0.5px;">No. Dokumen</td>
                        <td style="color: #0f172a; font-size: 14px; font-weight: bold;">{{ $document->no_doc ?? '-' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; padding-left: 0; letter-spacing: 0.5px;">Judul</td>
                        <td style="color: #0f172a; font-size: 14px; font-weight: 600;">{{ $document->title }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; padding-left: 0; letter-spacing: 0.5px;">Divisi/Instansi</td>
                        <td style="color: #0f172a; font-size: 14px; font-weight: 600;">{{ $document->division->name ?? 'Internal' }}</td>
                    </tr>
                </table>

                <div style="text-align: center; margin-top: 40px;">
                    <a href="{{ $url }}" style="background-color: {{ $headerColor }}; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        Lihat Dokumen
                    </a>
                </div>

                <p style="font-size: 12px; color: #94a3b8; margin-top: 40px; text-align: center; line-height: 1.6;">
                    Pesan ini dihasilkan secara otomatis oleh Sistem E-Arsip. <br>Harap tidak membalas email ini.
                </p>
            </td>
        </tr>
    </table>

</body>
</html>