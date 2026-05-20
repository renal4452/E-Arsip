<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;

class DocumentSignedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $document;

    /**
     * Menyuntikkan data dokumen ke dalam email
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Merakit subjek dan tampilan email
     */
    public function build()
    {
        return $this->subject('✅ PENGESAHAN: Dokumen Anda Telah Ditandatangani (TTE)')
                    ->view('emails.document_signed');
    }
}