<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="container">
        <div class="card" style="text-align: center;">
            <div class="success-icon" style="color: var(--error);">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <h1>Invalid QR Code</h1>
            <p class="subtitle">{{ $message }}</p>
            
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 2rem;">
                Please ask the mentor to show the current QR code and scan it again.
            </p>
        </div>
    </div>
</body>
</html>
