<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $messageText;
    public $type;
    public $url;

    /**
     * Create a new message instance.
     * $type bisa berupa: 'success' (Hijau/ACC), 'danger' (Merah/Revisi), 'info' (Biru)
     */
    public function __construct($document, $messageText, $type = 'info')
    {
        $this->document = $document;
        $this->messageText = $messageText;
        $this->type = $type;
        $this->url = route('documents.show', $document->id); // Link langsung ke detail dokumen
    }

    public function build()
    {
        $subject = "[E-Arsip] Pembaruan Status Dokumen: " . ($this->document->no_doc ?? 'Tanpa Nomor');

        return $this->subject($subject)
                    ->view('emails.document_notification');
    }
}