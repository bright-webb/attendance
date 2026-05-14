<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Show mentor login page if not authenticated, else show QR code.
     */
    public function mentorView(Request $request)
    {
        if ($request->session()->get('mentor_authenticated') !== true) {
            return view('mentor-login');
        }
        return view('mentor-qr');
    }

    /**
     * Authenticate mentor PIN.
     */
    public function mentorAuthenticate(Request $request)
    {
        $request->validate(['pin' => 'required']);
        $correctPin = env('MENTOR_PIN', '1234');

        if ($request->pin === $correctPin) {
            $request->session()->put('mentor_authenticated', true);
            return redirect()->route('mentor.qr');
        }

        return back()->withErrors(['message' => 'Incorrect PIN']);
    }

    /**
     * Generate a new short-lived token for the QR code.
     * Called via AJAX every 60 seconds from the mentor page.
     */
    public function generateToken()
    {
        $token = Str::random(32);
        // Store in cache for 90 seconds (buffer beyond the 60s refresh cycle)
        Cache::put("attendance_token_{$token}", true, 90);

        return response()->json([
            'token' => $token,
            'url' => route('attendance.scan', ['token' => $token])
        ]);
    }

    /**
     * Student's view - reached by scanning the QR code.
     * Validates the token is still alive before showing the form.
     */
    public function scan($token)
    {
        if (!Cache::has("attendance_token_{$token}")) {
            return view('attendance-error', [
                'message' => 'This QR code has expired. Please scan the latest one from the screen.'
            ]);
        }

        return view('sign-in', ['token' => $token]);
    }

    /**
     * Build a device fingerprint from the request to prevent abuse.
     * Combines IP + User-Agent into a unique daily key.
     */
    protected function getDeviceFingerprint(Request $request)
    {
        $ip = $request->ip();
        $ua = $request->header('User-Agent', 'unknown');
        return md5($ip . '|' . $ua . '|' . date('Y-m-d'));
    }

    /**
     * Submit attendance - validates token, checks for duplicate device,
     * then marks the user present in Google Sheets.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'token' => 'required|string'
        ]);

        $username = strtolower(trim($request->username));

        // 1. Validate token is still alive
        if (!Cache::has("attendance_token_{$request->token}")) {
            return back()
                ->withInput()
                ->withErrors(['message' => 'QR code expired. Please scan the latest QR code again.']);
        }

        // 2. Check device fingerprint — prevent same device from signing in twice today
        $fingerprint = $this->getDeviceFingerprint($request);
        $deviceKey = "attendance_device_{$fingerprint}";

        if (Cache::has($deviceKey)) {
            $previousUser = Cache::get($deviceKey);
            return back()
                ->withInput()
                ->withErrors(['message' => "This device has already been used to sign in today (by '{$previousUser}'). You cannot sign in for someone else."]);
        }

        // 3. Check username-level duplicate — prevent the same username being signed in twice
        $usernameKey = "attendance_user_{$username}_" . date('Y-m-d');
        if (Cache::has($usernameKey)) {
            return back()
                ->withInput()
                ->withErrors(['message' => 'You have already signed in today.']);
        }

        // 4. Mark present in Google Sheet (lazy-load the service only here)
        try {
            $googleSheet = app(GoogleSheetService::class);
            $result = $googleSheet->markPresent($username);
        } catch (\Exception $e) {
            Log::error("Google Sheets error: " . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['message' => 'Something went wrong connecting to Google Sheets. Please try again.']);
        }

        if ($result['success']) {
            // Lock this device and username for the rest of the day
            $secondsUntilMidnight = strtotime('tomorrow') - time();
            Cache::put($deviceKey, $username, $secondsUntilMidnight);
            Cache::put($usernameKey, true, $secondsUntilMidnight);

            return view('attendance-success', ['username' => $username]);
        }

        return back()
            ->withInput()
            ->withErrors(['message' => $result['message']]);
    }
}
