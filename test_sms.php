<?php

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/core/Model.php';
require_once BASE_PATH . '/app/services/SmsService.php';

echo "<pre>";
echo "=== SMS Service Test script ===\n\n";

$db = new Database();
$conn = $db->connect();
if (!$conn) {
    die("Database connection failed!\n");
}

// Find a test user (kullanici_id = 2 exists in user metadata)
$stmt = $conn->query("SELECT kullanici_id, telefon, ad, soyad FROM kullanici WHERE kullanici_id = 2 LIMIT 1");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Test user (kullanici_id = 2) not found in the database. Please make sure user exists.\n");
}

$telefon = $user['telefon'] ?? '5555555555';
$kullanici_id = $user['kullanici_id'];
$adSoyad = $user['ad'] . ' ' . $user['soyad'];

echo "Test Student: $adSoyad ($telefon) [ID: $kullanici_id]\n\n";

// 1. Test generic send()
echo "Testing send()...\n";
$r1 = SmsService::send($telefon, "Bu bir genel test mesajıdır.", $kullanici_id);
echo "Result: " . ($r1 ? "SUCCESS" : "FAILED") . "\n\n";

// 2. Test sendApplicationApproved()
echo "Testing sendApplicationApproved()...\n";
$r2 = SmsService::sendApplicationApproved($telefon, $kullanici_id, $adSoyad);
echo "Result: " . ($r2 ? "SUCCESS" : "FAILED") . "\n\n";

// 3. Test sendApplicationRejected()
echo "Testing sendApplicationRejected()...\n";
$r3 = SmsService::sendApplicationRejected($telefon, $kullanici_id, $adSoyad);
echo "Result: " . ($r3 ? "SUCCESS" : "FAILED") . "\n\n";

// 4. Test sendDocumentApproved()
echo "Testing sendDocumentApproved()...\n";
$r4 = SmsService::sendDocumentApproved($telefon, $kullanici_id, $adSoyad, "Staj Başvuru Formu");
echo "Result: " . ($r4 ? "SUCCESS" : "FAILED") . "\n\n";

// 5. Test sendDocumentRejected()
echo "Testing sendDocumentRejected()...\n";
$r5 = SmsService::sendDocumentRejected($telefon, $kullanici_id, $adSoyad, "Staj Başvuru Formu");
echo "Result: " . ($r5 ? "SUCCESS" : "FAILED") . "\n\n";

// 6. Test sendLeaveApproved()
echo "Testing sendLeaveApproved()...\n";
$r6 = SmsService::sendLeaveApproved($telefon, $kullanici_id, $adSoyad, "2026-07-25", "2026-07-28");
echo "Result: " . ($r6 ? "SUCCESS" : "FAILED") . "\n\n";

// 7. Test sendLeaveRejected()
echo "Testing sendLeaveRejected()...\n";
$r7 = SmsService::sendLeaveRejected($telefon, $kullanici_id, $adSoyad, "2026-07-25", "2026-07-28");
echo "Result: " . ($r7 ? "SUCCESS" : "FAILED") . "\n\n";

// 8. Test sendAttendanceWarning()
echo "Testing sendAttendanceWarning()...\n";
$r8 = SmsService::sendAttendanceWarning($telefon, $kullanici_id, $adSoyad, "2026-07-22", "İşe geç gelme");
echo "Result: " . ($r8 ? "SUCCESS" : "FAILED") . "\n\n";

// 9. Test sendProjectReminder()
echo "Testing sendProjectReminder()...\n";
$r9 = SmsService::sendProjectReminder($telefon, $kullanici_id, $adSoyad, "Staj Değerlendirme Raporu", "2026-08-10");
echo "Result: " . ($r9 ? "SUCCESS" : "FAILED") . "\n\n";

echo "=== Verifying Database Logs ===\n\n";

echo "--- Last 5 entries in 'sms' table ---\n";
$smsList = $conn->query("SELECT * FROM sms ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($smsList);

echo "\n--- Last 5 entries in 'sms_log' table ---\n";
$logList = $conn->query("SELECT * FROM sms_log ORDER BY sms_id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($logList);

echo "</pre>";
