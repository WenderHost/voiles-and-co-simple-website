<?php
$payload = [
  'ok' => false,
];

function json_response($status, $payload) {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  json_response(405, [
    'ok' => false,
    'error' => 'Method not allowed.'
  ]);
}

$webroot = dirname(__DIR__, 2);
$config_path = dirname($webroot) . '/config.php';
$config_relative = '..' . DIRECTORY_SEPARATOR . basename($config_path);
if (!is_readable($config_path)) {
  json_response(500, [
    'ok' => false,
    'error' => 'Missing config.php. Create it one directory above the webroot and add your SMTP2GO and Turnstile settings.',
    'instructions' => "Expected: {$config_relative}"
  ]);
}

$config = require $config_path;
if (!is_array($config)) {
  json_response(500, [
    'ok' => false,
    'error' => 'Invalid config.php. It should return an associative array.'
  ]);
}

$required = ['smtp2go_api_key', 'contact_to', 'from_email', 'from_name', 'email_subject', 'turnstile_secret'];
foreach ($required as $key) {
  if (empty($config[$key])) {
    json_response(500, [
      'ok' => false,
      'error' => "Missing config value: {$key}.",
      'instructions' => 'Update config.php with required settings.'
    ]);
  }
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');
$turnstile_token = trim($_POST['cf-turnstile-response'] ?? '');

if ($name === '' || $email === '') {
  json_response(400, [
    'ok' => false,
    'error' => 'Please provide your name and email.'
  ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_response(400, [
    'ok' => false,
    'error' => 'Please provide a valid email address.'
  ]);
}

if ($turnstile_token === '') {
  json_response(400, [
    'ok' => false,
    'error' => 'Turnstile validation failed. Please try again.',
    'turnstile_reset' => true
  ]);
}

$verify_payload = http_build_query([
  'secret' => $config['turnstile_secret'],
  'response' => $turnstile_token,
  'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
]);

$verify_ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
curl_setopt($verify_ch, CURLOPT_POST, true);
curl_setopt($verify_ch, CURLOPT_POSTFIELDS, $verify_payload);
curl_setopt($verify_ch, CURLOPT_RETURNTRANSFER, true);
$verify_result = curl_exec($verify_ch);
$verify_http = curl_getinfo($verify_ch, CURLINFO_HTTP_CODE);
curl_close($verify_ch);

$verify_data = json_decode($verify_result ?: '', true);
if ($verify_http !== 200 || empty($verify_data['success'])) {
  json_response(400, [
    'ok' => false,
    'error' => 'Spam protection check failed. Please try again.',
    'turnstile_reset' => true
  ]);
}

$subject_template = trim($config['email_subject'] ?? '');
$subject = strtr($subject_template, [
  '{name}' => $name
]);
$clean_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$clean_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$clean_service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
$clean_message = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

$html_body = '<!doctype html>'
  . '<html><body style="margin:0;padding:0;background:#f4f7f9;font-family:Arial,Helvetica,sans-serif;">'
  . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f9;padding:24px 0;">'
  . '<tr><td align="center">'
  . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 12px 30px rgba(0,0,0,.08);">'
  . '<tr><td style="background:#0b2a3a;color:#ffffff;padding:20px 28px;font-size:16px;letter-spacing:.6px;font-weight:700;">VOILES &amp; CO.</td></tr>'
  . '<tr><td style="padding:24px 28px;color:#0b1f2a;">'
  . '<h2 style="margin:0 0 14px;font-size:20px;color:#0b1f2a;">New Contact Inquiry</h2>'
  . '<p style="margin:0 0 10px;">The following contact was sent via the form found on <a href="https://voilesandco.com">the Voiles &amp; Co. website</a>.</p>'
  . '<p style="margin:0 0 10px;"><strong>Name:</strong> ' . $clean_name . '</p>'
  . '<p style="margin:0 0 10px;"><strong>Email:</strong> ' . $clean_email . '</p>'
  . '<p style="margin:0 0 10px;"><strong>Service needed:</strong> ' . ($clean_service !== '' ? $clean_service : 'Not specified') . '</p>'
  . '<p style="margin:0 0 10px;"><strong>Message:</strong><br/>' . ($clean_message !== '' ? $clean_message : 'No message provided.') . '</p>'
  . '</td></tr>'
  . '<tr><td style="padding:18px 28px;background:#f8fbfd;color:#5a6b75;font-size:12px;">Sent from the Voiles &amp; Co. website contact form.</td></tr>'
  . '</table>'
  . '</td></tr></table>'
  . '</body></html>';

$text_body = "New Contact Inquiry\n\n"
  . "Name: {$name}\n"
  . "Email: {$email}\n"
  . "Service needed: " . ($service !== '' ? $service : 'Not specified') . "\n\n"
  . "Message:\n" . ($message !== '' ? $message : 'No message provided.') . "\n";

$sender_email = trim($config['from_email']);
$sender_name = trim(str_replace(["\r", "\n"], '', $name));
$sender = $sender_email;
if ($sender_name !== '') {
  $sender = $sender_name . ' <' . $sender_email . '>';
}
$reply_to = trim(str_replace(["\r", "\n"], '', $email));

$email_payload = [
  'sender' => $sender,
  'to' => [$config['contact_to']],
  'subject' => $subject,
  'custom_headers' => [
    [
      'header' => 'Reply-To',
      'value' => $reply_to
    ]
  ],
  'html_body' => $html_body,
  'text_body' => $text_body
];

$send_ch = curl_init('https://api.smtp2go.com/v3/email/send');
curl_setopt($send_ch, CURLOPT_POST, true);
curl_setopt($send_ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'X-Smtp2go-Api-Key: ' . $config['smtp2go_api_key']
]);
curl_setopt($send_ch, CURLOPT_POSTFIELDS, json_encode($email_payload));
curl_setopt($send_ch, CURLOPT_RETURNTRANSFER, true);
$send_result = curl_exec($send_ch);
$send_http = curl_getinfo($send_ch, CURLINFO_HTTP_CODE);
curl_close($send_ch);

$send_data = json_decode($send_result ?: '', true);
if ($send_http !== 200 || empty($send_data['data']['succeeded']) || $send_data['data']['succeeded'] < 1) {
  json_response(500, [
    'ok' => false,
    'error' => 'Email delivery failed. Please try again later.'
  ]);
}

json_response(200, [
  'ok' => true
]);
