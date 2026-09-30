<?php

declare(strict_types=1);

/**
 * اتصال مرکزی به دیتابیس با PDO.
 * اطلاعات اتصال را از Environment Variables می‌خواند.
 */
function db(): PDO
{
    static $pdo = null;

    // در هر درخواست، اتصال را دوباره نساز
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'safari_web';
    $user = getenv('DB_USER') ?: 'safari_web';
    $pass = getenv('DB_PASS') ?: 'mM$s9908035538';

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return $pdo;
    } catch (PDOException $e) {
        // جزئیات خطا فقط در log سرور ثبت شود، نه در خروجی سایت
        error_log('Database connection error: ' . $e->getMessage());

        throw new RuntimeException(
            'اتصال به دیتابیس برقرار نشد. تنظیمات دیتابیس را بررسی کنید.'
        );
    }
}