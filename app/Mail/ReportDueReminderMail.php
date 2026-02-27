<?php

namespace App\Mail;

use App\Models\AdoptionCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportDueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdoptionCase $case,
        public int $daysUntilDue
    ) {}

    public function envelope(): Envelope
    {
        $petName = $this->case->pet->name ?? '毛孩';
        return new Envelope(
            subject: "【追蹤提醒】{$petName} 的回報期限即將到來",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-due-reminder',
        );
    }
}
