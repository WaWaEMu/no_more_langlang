<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #E6E9EF;
        }

        .container {
            max-width: 560px;
            margin: 32px auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .header {
            background-color:
                {{ $daysOverdue >= 7 ? '#9b2c2c' : ($daysOverdue >= 3 ? '#c53030' : '#dd6b20') }}
            ;
            color: #fff;
            padding: 24px 32px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .body {
            padding: 32px;
        }

        .pet-badge {
            display: inline-block;
            background: #E6E9EF;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            color: #2c5282;
            margin-bottom: 16px;
        }

        .alert-box {
            background:
                {{ $daysOverdue >= 7 ? '#fff5f5' : ($daysOverdue >= 3 ? '#fffaf0' : '#fffff0') }}
            ;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 16px 0;
            border-left: 4px solid
                {{ $daysOverdue >= 7 ? '#9b2c2c' : ($daysOverdue >= 3 ? '#c53030' : '#dd6b20') }}
            ;
        }

        .footer {
            text-align: center;
            padding: 16px 32px 24px;
            color: #a0aec0;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            background:
                {{ $daysOverdue >= 7 ? '#9b2c2c' : '#c53030' }}
            ;
            color: #fff !important;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if($daysOverdue >= 7)
                <h1>⚠️ 嚴重警告：回報已逾期 {{ $daysOverdue }} 天</h1>
            @elseif($daysOverdue >= 3)
                <h1>⚠ 逾期關切：回報已逾期 {{ $daysOverdue }} 天</h1>
            @else
                <h1>📢 逾期提醒：回報已逾期 {{ $daysOverdue }} 天</h1>
            @endif
        </div>
        <div class="body">
            <div class="pet-badge">🐾 {{ $case->pet->name ?? '毛孩' }}</div>
            <p>您好，</p>

            @if($daysOverdue >= 7)
                <p><strong>{{ $case->pet->name ?? '毛孩' }}</strong> 的認養人已超過 <strong>{{ $daysOverdue }} 天</strong>未提交追蹤回報。</p>
                <p>建議您儘速與認養人取得聯繫，確認毛孩的現況與安全。</p>
            @elseif($daysOverdue >= 3)
                <p><strong>{{ $case->pet->name ?? '毛孩' }}</strong> 的認養人已逾期 <strong>{{ $daysOverdue }} 天</strong>未提交追蹤回報。</p>
                <p>您可以考慮主動聯繫認養人了解狀況。</p>
            @else
                <p><strong>{{ $case->pet->name ?? '毛孩' }}</strong> 的認養人已逾期 <strong>{{ $daysOverdue }} 天</strong>未提交追蹤回報。</p>
                <p>認養人可能只是太忙而忘記了，系統已持續發送提醒中。</p>
            @endif

            <div class="alert-box">
                <strong>📅 原定回報日：</strong>{{ \Carbon\Carbon::parse($case->next_report_due_at)->format('Y/m/d') }}
            </div>

            <a href="{{ url('/user/adoptions') }}" class="btn">前往查看案件</a>
        </div>
        <div class="footer">
            此信件由「諾摩浪浪」系統自動發送，請勿直接回覆。
        </div>
    </div>
</body>

</html>