<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background-color: #E6E9EF; }
        .container { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background-color: #2c5282; color: #fff; padding: 24px 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .body { padding: 32px; }
        .pet-badge { display: inline-block; background: #E6E9EF; border-radius: 8px; padding: 8px 16px; font-weight: 600; color: #2c5282; margin-bottom: 16px; }
        .report-box { background: #f7fafc; border-left: 4px solid #2c5282; border-radius: 0 8px 8px 0; padding: 16px; margin: 16px 0; }
        .report-box p { margin: 0; color: #4a5568; white-space: pre-wrap; }
        .footer { text-align: center; padding: 16px 32px 24px; color: #a0aec0; font-size: 12px; }
        .btn { display: inline-block; background: #2c5282; color: #fff !important; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 追蹤回報通知</h1>
        </div>
        <div class="body">
            <div class="pet-badge">🐾 {{ $case->pet->name ?? '毛孩' }}</div>
            <p>您好，</p>
            <p><strong>{{ $case->pet->name ?? '毛孩' }}</strong> 的認養人已提交了一份新的追蹤回報：</p>
            <div class="report-box">
                <p>{{ $reportContent }}</p>
            </div>
            <p>您可以點擊下方按鈕登入網站查看完整的回報內容與照片。</p>
            <a href="{{ url('/user/adoptions') }}" class="btn">前往查看回報</a>
        </div>
        <div class="footer">
            此信件由「諾摩浪浪」系統自動發送，請勿直接回覆。
        </div>
    </div>
</body>
</html>
