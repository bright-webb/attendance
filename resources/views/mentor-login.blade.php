<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Login | Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Mentor Access</h1>
            <p class="subtitle">Enter the PIN to access the QR Code scanner.</p>

            @if($errors->any())
                <div class="error-msg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('mentor.login') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="pin">Mentor PIN</label>
                    <input type="password" id="pin" name="pin" placeholder="Enter PIN" required autofocus>
                </div>

                <button type="submit">
                    Unlock Scanner
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
