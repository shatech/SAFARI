<?php

require_once __DIR__ . '/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pdo = db();

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf()
{
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(419);
        exit('درخواست معتبر نیست. صفحه را دوباره بارگذاری کنید.');
    }
}

function make_slug($value)
{
    $value = trim((string) $value);
    $value = mb_strtolower($value, 'UTF-8');
    $value = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value);
    $value = trim($value, '-');

    return $value;
}

function redirect_to($url)
{
    header('Location: ' . $url);
    exit;
}