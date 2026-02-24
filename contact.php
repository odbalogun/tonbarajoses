<?php
/**
 * Contact form handler - sends email from the contact modal.
 * Requires PHP mail() to be configured on the server.
 */

$to = 'tonbara@tonbarajoses.com';
$redirect_success = 'index.html';
$redirect_error = 'index.html?contact_error=1';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect_success);
    exit;
}

// Get and sanitize input
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Validate
$errors = [];
if (empty($name)) {
    $errors[] = 'Name is required.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}
if (empty($message)) {
    $errors[] = 'Message is required.';
}

if (!empty($errors)) {
    header('Location: ' . $redirect_error);
    exit;
}

// Build email
$subject = 'Contact form: ' . substr($name, 0, 50);
$body = "Name: $name\n";
$body .= "Email: $email\n\n";
$body .= "Message:\n$message";

$headers = [
    'From: ' . $email,
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

// Redirect back - preserve referring page if possible
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $redirect_success;
$base = preg_replace('/\?.*$/', '', $referer);

if ($sent) {
    header('Location: ' . $base . '?contact_sent=1');
} else {
    header('Location: ' . $base . '?contact_error=1');
}
exit;
