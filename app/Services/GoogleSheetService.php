<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    
    const MAIN_SHEET = 'Attendance';
    const LOGS_SHEET = 'SIGN-IN LOGS';

    public function __construct()
    {
        $this->spreadsheetId = config('services.google.sheet_id');
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/service-account.json'));
        $this->client->addScope(Sheets::SPREADSHEETS);
        $this->service = new Sheets($this->client);
    }


    public function findUserRow($username)
    {
        $sheet = self::MAIN_SHEET;
        $range = "{$sheet}!A:A";
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            return null;
        }

        foreach ($values as $index => $row) {
            if (isset($row[0]) && strtolower(trim($row[0])) === strtolower(trim($username))) {
                return $index + 1; 
            }
        }

        return null;
    }

   
    public function findDateColumn()
    {
        $today = date('n/j'); 
        $sheet = self::MAIN_SHEET;
        $range = "{$sheet}!1:1";
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values) || !isset($values[0])) {
            return null;
        }

        foreach ($values[0] as $index => $header) {
            if (trim($header) === $today) {
                return $this->columnIndexToLetter($index);
            }
        }

        return null;
    }

    
    public function isAlreadyPresent($username)
    {
        $row = $this->findUserRow($username);
        if (!$row) return false;

        $col = $this->findDateColumn();
        if (!$col) return false;

        $sheet = self::MAIN_SHEET;
        $range = "{$sheet}!{$col}{$row}";
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if (!empty($values) && isset($values[0][0])) {
            return strtolower(trim($values[0][0])) === 'present';
        }

        return false;
    }

 
    public function markPresent($username)
    {
        $row = $this->findUserRow($username);
        if (!$row) {
            return ['success' => false, 'message' => "Username '{$username}' not found in the attendance sheet."];
        }

        $col = $this->findDateColumn();
        if (!$col) {
            return ['success' => false, 'message' => "Today's date column (" . date('n/j') . ") was not found in the sheet."];
        }

        if ($this->isAlreadyPresent($username)) {
            return ['success' => false, 'message' => "You have already signed in today."];
        }

        $sheet = self::MAIN_SHEET;
        $params = ['valueInputOption' => 'RAW'];

        $range = "{$sheet}!{$col}{$row}";
        $body = new Sheets\ValueRange([
            'values' => [['Present']]
        ]);
        $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);

    
        $timestampRange = "{$sheet}!E{$row}";
        $timestampBody = new Sheets\ValueRange([
            'values' => [[date('Y-m-d H:i:s')]]
        ]);
        $this->service->spreadsheets_values->update($this->spreadsheetId, $timestampRange, $timestampBody, $params);

       
        $this->logToHistory($username);

        return ['success' => true, 'message' => 'Attendance marked successfully!'];
    }

    protected function logToHistory($username)
    {
        $sheet = self::LOGS_SHEET;
        $range = "'{$sheet}'!A:D";

        try {
            // Check if headers exist
            $existing = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $existing->getValues();

            if (empty($values)) {
                $headerBody = new Sheets\ValueRange([
                    'values' => [['Username', 'Date', 'Time', 'Device Fingerprint']]
                ]);
                $this->service->spreadsheets_values->append(
                    $this->spreadsheetId, $range, $headerBody,
                    ['valueInputOption' => 'RAW']
                );
            }

            // Append the log entry
            $body = new Sheets\ValueRange([
                'values' => [[$username, date('Y-m-d'), date('H:i:s'), request()?->header('User-Agent', 'Unknown')]]
            ]);
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId, $range, $body,
                ['valueInputOption' => 'RAW']
            );

        } catch (\Exception $e) {
            Log::error("Failed to log to SIGN-IN LOGS: " . $e->getMessage());
        }
    }

    protected function columnIndexToLetter($index)
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intval($index / 26) - 1;
        }
        return $letter;
    }
}
