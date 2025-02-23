<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class InvoiceApprovalNotification extends Notification
{
    use Queueable;

    public $document;
    public $action;
    public $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($document, $action, $role)
    {
        $this->document = $document;
        $this->action = $action;
        $this->role = $role;
    }

    /**
     * Tentukan channel notifikasi.
     */
    public function via($notifiable)
    {
        return ['database']; // Simpan dalam database
    }

    /**
     * Simpan data notifikasi dalam database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'invoice_number' => $this->document->invoice_number,
            'action' => $this->action,
            'message' => "Invoice #{$this->document->invoice_number} membutuhkan persetujuan dari {$this->role}.",
            'url' => route('management-non-fee.show', $this->document->id),
        ];
    }
}