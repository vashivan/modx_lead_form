<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['lead_submit'])) {
    return '';
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');
$website = trim($_POST['website'] ?? '');

$errors = [];

if (!empty($website)) {
    $errors[] = 'Spam detected.';
}

if ($name === '' || mb_strlen($name) < 2) {
    $errors[] = 'Name is too short.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email.';
}

if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
    $errors[] = 'Invalid phone.';
}

if (!empty($errors)) {
    return '<div style="color:red;">'
        . implode('<br>', array_map('htmlspecialchars', $errors))
        . '</div>';
}

$payload = [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'message' => $message,
    'source' => 'modx_lead_form',
    'created_at' => date('Y-m-d H:i:s'),
];

$apiUrl = 'https://webhook.site/ce5c3d85-0bca-43db-a231-3111569a403a';

$apiStatus = 'pending';
$apiResponse = null;

try {
    $ch = curl_init($apiUrl);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 10,
    ]);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $apiStatus = 'failed';

        $modx->log(
            modX::LOG_LEVEL_ERROR,
            '[LeadForm] API cURL error: ' . curl_error($ch)
        );
    } elseif ($httpCode < 200 || $httpCode >= 300) {
        $apiStatus = 'failed';

        $modx->log(
            modX::LOG_LEVEL_ERROR,
            '[LeadForm] API HTTP error: ' . $httpCode . ' Response: ' . $apiResponse
        );
    } else {
        $apiStatus = 'success';
    }

    curl_close($ch);
} catch (Exception $e) {
    $apiStatus = 'failed';
    $apiResponse = $e->getMessage();

    $modx->log(
        modX::LOG_LEVEL_ERROR,
        '[LeadForm] API exception: ' . $e->getMessage()
    );
}

try {
    $stmt = $modx->prepare("
        INSERT INTO modx_leads
        (name, email, phone, message, api_status, api_response)
        VALUES (:name, :email, :phone, :message, :api_status, :api_response)
    ");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':message' => $message,
        ':api_status' => $apiStatus,
        ':api_response' => $apiResponse,
    ]);
} catch (Exception $e) {
    $modx->log(
        modX::LOG_LEVEL_ERROR,
        '[LeadForm] DB error: ' . $e->getMessage()
    );

    return '<div style="color:red;">Database error. Please try again later.</div>';
}

return '<div style="color:green;">Thank you! Form submitted successfully.</div>';