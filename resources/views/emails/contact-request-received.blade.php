<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Quote Request</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 32px 16px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); padding: 32px; color: white; }
        .header h1 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
        .header p { margin: 0; font-size: 13px; opacity: 0.8; }
        .body { padding: 32px; }
        .label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 15px; color: #111827; margin-bottom: 20px; }
        .message-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; font-size: 14px; color: #374151; line-height: 1.6; white-space: pre-wrap; }
        .badge { display: inline-block; background: #dbeafe; color: #1d4ed8; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px; margin-bottom: 20px; }
        .footer { padding: 20px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
        .cta { display: inline-block; margin-top: 24px; background: #1d4ed8; color: white; font-weight: 600; font-size: 13px; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>New Quote Request</h1>
            <p>Submitted via the CCC public website</p>
        </div>
        <div class="body">
            <div class="badge">{{ $contactRequest->service }}</div>

            <div class="label">Name</div>
            <div class="value">{{ $contactRequest->name }}</div>

            <div class="label">Email</div>
            <div class="value"><a href="mailto:{{ $contactRequest->email }}" style="color:#1d4ed8;">{{ $contactRequest->email }}</a></div>

            @if($contactRequest->phone)
            <div class="label">Phone</div>
            <div class="value">{{ $contactRequest->phone }}</div>
            @endif

            <div class="label">Project Details</div>
            <div class="message-box">{{ $contactRequest->message }}</div>

            <a href="{{ url('/app') }}" class="cta">View in Dashboard</a>
        </div>
        <div class="footer">
            This email was sent automatically by CCC Ops when a quote request was submitted on the public website.
            Submitted {{ $contactRequest->created_at->format('D, d M Y \a\t g:ia') }}.
        </div>
    </div>
</body>
</html>
