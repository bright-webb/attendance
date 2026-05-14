<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Scanner | Mentor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Class Attendance</h1>
            <p class="subtitle">Please scan the QR code to sign in. This code refreshes every 60 seconds.</p>
            
            <div class="qr-container">
                <div id="qrcode"></div>
            </div>

            <div class="timer-bar">
                <div id="timer-progress" class="timer-progress"></div>
            </div>
            
            <p id="timer-text" style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 0.75rem;">
                Refreshing in 60s
            </p>
        </div>
    </div>

    <script>
        const qrElement = document.getElementById('qrcode');
        const progressElement = document.getElementById('timer-progress');
        const timerText = document.getElementById('timer-text');
        let qrcode = new QRCode(qrElement, {
            width: 256,
            height: 256,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        let timeLeft = 60;

        async function refreshQR() {
            try {
                const response = await fetch('{{ route('api.token') }}');
                const data = await response.json();
                qrcode.clear();
                qrcode.makeCode(data.url);
                timeLeft = 60;
                updateProgress();
            } catch (error) {
                console.error('Failed to refresh token:', error);
            }
        }

        function updateProgress() {
            const percentage = (timeLeft / 60) * 100;
            progressElement.style.width = percentage + '%';
            timerText.innerText = `Refreshing in ${timeLeft}s`;
            
            if (timeLeft <= 0) {
                refreshQR();
            } else {
                timeLeft--;
                setTimeout(updateProgress, 1000);
            }
        }

        // Initial load
        refreshQR();
    </script>
</body>
</html>
