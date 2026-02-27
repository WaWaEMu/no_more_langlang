<?php

namespace App\Mail;

use App\Models\AdoptionCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportOverdueAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdoptionCase $case,
        public int $daysOverdue
    ) {
    }

    public function envelope(): Envelope
    {
        $petName = $this->case->pet->name ?? '毛孩';

        $urgency = match (true) {
            $this->daysOverdue >= 7 => '⚠️ 嚴重警告',
            $this->daysOverdue >= 3 => '⚠ 逾期關切',
            default => '📢 逾期提醒',
        };

        return new Envelope(
            subject: "【{$urgency}】{$petName} 的追蹤回報已逾期 {$this->daysOverdue} 天",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-overdue-alert',
        );
    }
}
