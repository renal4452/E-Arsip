<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    private $document;
    private $message;
    private $type; // 'success', 'warning', 'danger'

    public function __construct($document, $message, $type = 'info')
    {
        $this->document = $document;
        $this->message = $message;
        $this->type = $type;
    }

    // Kita arahkan agar notifikasi ini disimpan ke Database
    public function via($notifiable)
    {
        return ['database'];
    }

    // Data apa saja yang mau kita simpan dan tampilkan di Lonceng
    public function toArray($notifiable)
    {
        return [
            'doc_id' => $this->document->id,
            'no_doc' => $this->document->no_doc ?? 'Tanpa Nomor',
            'title'  => $this->document->title,
            'message'=> $this->message,
            'type'   => $this->type, // Untuk warna ikon di navbar nanti
        ];
    }
}