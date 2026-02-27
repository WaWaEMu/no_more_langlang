<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background-color: #E6E9EF; }
        .container { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background-color: {{ $daysUntilDue <= 2 ? '#c53030' : '#2c5282' }}; color: #fff; padding: 24px 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .body { padding: 32px; }
        .pet-badge { display: inline-block; background: #E6E9EF; border-radius: 8px; padding: 8px 16px; font-weight: 600; color: #2c5282; margin-bottom: 16px; }
        .highlight { background: {{ $daysUntilDue <= 2 ? '#fff5f5' : '#fefcbf' }}; border-radius: 8px; padding: 12px 16px; margin: 16px 0; border-left: 4px solid {{ $daysUntilDue <= 2 ? '#c53030' : '#d69e2e' }}; }
        .footer { text-align: center; padding: 16px 32px 24px; color: #a0aec0; font-size: 12px; }
        .btn { display: inline-block; background: {{ $daysUntilDue <= 2 ? '#c53030' : '#2c5282' }}; color: #fff !important; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $daysUntilDue <= 2 ? '⏰ 回報即將逾期' : '🗓️ 追蹤回報提醒' }}</h1>
        </div>
        <div class="body">
            <div class="pet-badge">🐾 {{ $case->pet->name ?? '毛孩' }}</div>
            <p>您好，</p>
            @if($daysUntilDue <= 2)
                <p><strong>{{ $case->pet->name ?? '毛孩' }}</strong> 的追蹤回報期限即將在 <strong>{{ $daysUntilDue }}</strong> 天後到期，請務必抽空完成回報。</p>
            @else
                <p>提醒您，<strong>{{ $case->pet->name ?? '毛孩' }}</strong> 的下一份追蹤回報約在一週後（{{ $daysUntilDue }}天）到期。</p>
                <p>週末若有跟毛孩互動拍照，可以先預備好照片唷！</p>
            @endif

            <div class="highlight">
                <strong>📅 回報截止日：</strong>{{ \Carbon\Carbon::parse($case->next_report_due_at)->format('Y/m/d') }}
            </div>

            <a href="{{ url('/user/adopted') }}" class="btn">{{ $daysUntilDue <= 2 ? '立即前往回報' : '前往預備回報' }}</a>
        </div>
        <div class="footer">
            此信件由「諾摩浪浪」系統自動發送，請勿直接回覆。
        </div>
    </div>
</body>
</html>
