<?php

namespace App\Mail;

use App\Models\AdoptionCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrackingReportSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdoptionCase $case,
        public string $reportContent
    ) {}

    public function envelope(): Envelope
    {
        $petName = $this->case->pet->name ?? '毛孩';
        return new Envelope(
            subject: "【追蹤回報】{$petName} 的認養人提交了新回報",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tracking-report-submitted',
        );
    }
}
