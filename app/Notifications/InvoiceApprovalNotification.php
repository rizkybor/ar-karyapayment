<?php

namespace App\Notifications;


use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class InvoiceApprovalNotification extends Notification
{
    public $document;
    public $action;
    public $role;

    public function __construct($document, $action, $role)
    {
        $this->document = $document;
        $this->action = $action;
        $this->role = $role;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'invoice_number' => $this->document->invoice_number,
            'action' => $this->action,
            'message' => "Invoice #{$this->document->invoice_number} membutuhkan persetujuan dari {$this->role}.",
            'url' => route('notifications.show', $this->document->id),
        ];
    }
}