<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success | Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="container">
        <div class="card" style="text-align: center;">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h1>Success!</h1>
            <p class="subtitle">Thank you, <strong>{{ $username }}</strong>. Your attendance has been recorded in the Google Sheet.</p>
            
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 2rem;">
                You can now close this window.
            </p>
        </div>
    </div>
</body>
</html>
