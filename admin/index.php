<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$admin = require_admin();

$articleCounts = [
    'published' => 0,
    'draft'     => 0,
    'scheduled' => 0,
    'archived'  => 0,
];

$statsAvailable = true;

try {
    $stmt = db()->query(
        'SELECT status, COUNT(*) AS total
         FROM articles
         WHERE deleted_at IS NULL
         GROUP BY status'
    );

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (array_key_exists($row['status'], $articleCounts)) {
            $articleCounts[$row['status']] = (int) $row['total'];
        }
    }
} catch (Throwable $exception) {
    error_log('Admin dashboard error: ' . $exception->getMessage());
    $statsAvailable = false;
}

$totalArticles = array_sum($articleCounts);
$maxCount = max(1, ...array_values($articleCounts));

$statuses = [
    'published' => [
        'label' => 'منتشرشده',
        'color' => 'green',
    ],
    'draft' => [
        'label' => 'پیش‌نویس',
        'color' => '',
    ],
    'scheduled' => [
        'label' => 'زمان‌بندی‌شده',
        'color' => 'orange',
    ],
    'archived' => [
        'label' => 'بایگانی‌شده',
        'color' => 'red',
    ],
];

/*
|--------------------------------------------------------------------------
| تنظیمات سایدبار
|--------------------------------------------------------------------------
*/

$activePage = 'dashboard';
$sidebarArticleCount = $totalArticles;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>داشبورد مدیریت</title>

    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>
<div class="admin-shell">

    <input
        class="nav-toggle"
        type="checkbox"
        id="nav-toggle"
    >

    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <div class="workspace">

        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <label
                    class="icon-button mobile-menu-button"
                    for="nav-toggle"
                    aria-label="باز کردن منو"
                >
                    ☰
                </label>

                <div class="breadcrumb">
                    صفحات
                    <span> / </span>
                    <strong>داشبورد</strong>
                </div>
            </div>

            <div class="topbar-actions">
                <div class="profile">
                    <div class="avatar">
                        <?= e(mb_substr($admin['name'], 0, 1, 'UTF-8')) ?>
                    </div>

                    <div>
                        <div class="profile-name">
                            <?= e($admin['name']) ?>
                        </div>

                        <div class="profile-role">
                            مدیر سیستم
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">

            <div class="page-heading">
                <div>
                    <h1>
                        سلام، <?= e($admin['name']) ?> 👋
                    </h1>

                    <p>
                        وضعیت محتوای سایت را از اینجا دنبال کنید.
                    </p>
                </div>

                <a class="primary-button" href="article-create.php">
                    <span>＋</span>
                    افزودن مقاله
                </a>
            </div>

            <section class="stats-grid" aria-label="آمار مقالات">

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">کل مقالات</span>
                        <span class="stat-icon purple">▤</span>
                    </div>

                    <div class="stat-number">
                        <?= $statsAvailable ? $totalArticles : '—' ?>
                    </div>

                    <div class="stat-note">
                        مجموع محتوای ثبت‌شده
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">منتشرشده</span>
                        <span class="stat-icon green">✓</span>
                    </div>

                    <div class="stat-number">
                        <?= $statsAvailable ? $articleCounts['published'] : '—' ?>
                    </div>

                    <div class="stat-note">
                        مقالات قابل مشاهده در سایت
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">پیش‌نویس‌ها</span>
                        <span class="stat-icon orange">✎</span>
                    </div>

                    <div class="stat-number">
                        <?= $statsAvailable ? $articleCounts['draft'] : '—' ?>
                    </div>

                    <div class="stat-note">
                        محتوای ذخیره‌شده برای تکمیل
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">زمان‌بندی‌شده</span>
                        <span class="stat-icon red">◷</span>
                    </div>

                    <div class="stat-number">
                        <?= $statsAvailable ? $articleCounts['scheduled'] : '—' ?>
                    </div>

                    <div class="stat-note">
                        مقالات آمادهٔ انتشار در آینده
                    </div>
                </article>

            </section>

            <section class="dashboard-grid">

                <div class="panel">
                    <div class="panel-heading">
                        <h2>وضعیت محتوا</h2>
                        <span>نمای کلی مقالات</span>
                    </div>

                    <?php if (!$statsAvailable): ?>

                        <div class="notice">
                            دریافت آمار مقالات ممکن نشد.
                            ساختار جدول articles و اتصال دیتابیس را بررسی کنید.
                        </div>

                    <?php elseif ($totalArticles === 0): ?>

                        <div class="notice">
                            هنوز مقاله‌ای ثبت نشده است.
                            از دکمهٔ «افزودن مقاله» برای شروع استفاده کنید.
                        </div>

                    <?php else: ?>

                        <?php foreach ($statuses as $key => $status): ?>
                            <?php
                            $count = $articleCounts[$key];
                            $width = (int) round(($count / $maxCount) * 100);
                            ?>

                            <div class="status-row">
                                <span class="status-label">
                                    <?= e($status['label']) ?>
                                </span>

                                <div class="progress-track">
                                    <div
                                        class="progress-fill <?= e($status['color']) ?>"
                                        style="width: <?= $width ?>%"
                                    ></div>
                                </div>

                                <span class="status-count">
                                    <?= $count ?>
                                </span>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <h2>دسترسی سریع</h2>
                        <span>میانبرهای مدیریت</span>
                    </div>

                    <div class="quick-list">

                        <a class="quick-link" href="article-create.php">
                            <span class="quick-icon">＋</span>

                            <span>
                                <strong>نوشتن مقالهٔ جدید</strong>
                                <small>
                                    ایجاد و آماده‌سازی محتوای تازه
                                </small>
                            </span>

                            <span class="quick-arrow">←</span>
                        </a>

                        <a class="quick-link" href="articles.php">
                            <span class="quick-icon">▤</span>

                            <span>
                                <strong>مدیریت مقالات</strong>
                                <small>
                                    مشاهده و ویرایش محتوای سایت
                                </small>
                            </span>

                            <span class="quick-arrow">←</span>
                        </a>

                        <a class="quick-link" href="categories.php">
                            <span class="quick-icon">◈</span>

                            <span>
                                <strong>دسته‌بندی‌ها</strong>
                                <small>
                                    سامان‌دهی موضوعات وب‌سایت
                                </small>
                            </span>

                            <span class="quick-arrow">←</span>
                        </a>

                    </div>

                    <div class="notice">
                        بخش‌های مقالات، دسته‌بندی‌ها و تنظیمات را
                        در مراحل بعدی به پنل اضافه می‌کنیم.
                    </div>
                </div>

            </section>

        </main>
    </div>
</div>
</body>
</html>