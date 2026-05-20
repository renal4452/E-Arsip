<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengesahan Dokumen</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 0;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <tr>
            <td style="background-color: #10b981; padding: 35px 20px; text-align: center;">
                <h2 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: 1px;">Pengesahan Dokumen</h2>
                <p style="color: #ecfdf5; margin: 8px 0 0 0; font-size: 14px; font-weight: 500;">Sistem Manajemen Arsip Inspektorat</p>
            </td>
        </tr>

        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 15px; color: #475569; margin-top: 0; line-height: 1.7;">
                    Yth. <strong>Bapak/Ibu Pembuat Laporan</strong>,<br><br>
                    Pemberitahuan ini disampaikan secara otomatis oleh sistem bahwa dokumen yang Anda ajukan telah berhasil melalui tahap finalisasi dan telah dibubuhi <strong style="color: #0f172a;">Tanda Tangan Elektronik (TTE)</strong> oleh Inspektur.
                </p>

                <div style="background-color: #f8fafc; border-left: 4px solid #10b981; padding: 20px; border-radius: 4px; margin: 30px 0;">
                    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td width="35%" style="color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; padding-left: 0; border-bottom: 1px solid #f1f5f9;">Nomor Dokumen</td>
                            <td style="color: #0f172a; font-size: 14px; font-weight: bold; border-bottom: 1px solid #f1f5f9;">{{ $document->no_doc ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; padding-left: 0; border-bottom: 1px solid #f1f5f9;">Judul Laporan</td>
                            <td style="color: #0f172a; font-size: 14px; font-weight: 600; border-bottom: 1px solid #f1f5f9;">{{ $document->title }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; padding-left: 0; padding-bottom: 0;">Waktu Pengesahan</td>
                            <td style="color: #10b981; font-size: 14px; font-weight: 700; padding-bottom: 0;">{{ now()->format('d F Y - H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>

                <p style="font-size: 15px; color: #475569; line-height: 1.6; margin-bottom: 35px;">
                    Dokumen final yang sah secara hukum kini sudah dapat diunduh melalui <em>dashboard</em> Anda atau dengan menekan tombol di bawah ini.
                </p>

                <div style="text-align: center;">
                    <a href="{{ route('documents.show', $document->id) }}" style="background-color: #10b981; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);">
                        Lihat Berkas Final
                    </a>
                </div>
            </td>
        </tr>

        <tr>
            <td style="background-color: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.6;">
                    Email ini dihasilkan secara otomatis oleh sistem. Mohon untuk tidak membalas email ini.<br>
                    &copy; {{ date('Y') }} Inspektorat Daerah.
                </p>
            </td>
        </tr>
    </table>

</body>
</html>