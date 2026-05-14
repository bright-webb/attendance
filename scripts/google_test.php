<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$sheetId = getenv('GOOGLE_SHEET_ID') ?: 'MISSING_GOOGLE_SHEET_ID';
$authPath = __DIR__ . '/../storage/app/service-account.json';

if (!file_exists($authPath)) {
    fwrite(STDERR, "Auth file not found: {$authPath}\n");
    exit(1);
}

$client = new Client();
$client->setAuthConfig($authPath);
$client->addScope(Sheets::SPREADSHEETS);

$service = new Sheets($client);

try {
    $range = 'Attendance!A1:A5';
    $resp = $service->spreadsheets_values->get($sheetId, $range);
    $values = $resp->getValues();
    echo "SUCCESS\n";
    echo "Range: {$range}\n";
    print_r($values);
} catch (Throwable $e) {
    echo "FAILED\n";
    echo $e->getMessage() . "\n";
    exit(2);
}

