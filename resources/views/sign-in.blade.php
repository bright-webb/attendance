<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Sign In</h1>
            <p class="subtitle">Enter your Gitea username to mark your attendance for today.</p>

            @if($errors->any())
                <div class="error-msg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('attendance.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label for="username">Gitea Username</label>
                    <input type="text" id="username" name="username" placeholder="e.g. jdoe" value="{{ old('username') }}" required autofocus>
                </div>

                <button type="submit">
                    Mark Attendance
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </button>
            </form>
        </div>
    </div>
</body>
</html>