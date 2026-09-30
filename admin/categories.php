<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$admin = require_admin();

$categories = [];
$categoriesAvailable = true;
$errorMessage = '';

try {
    $stmt = db()->query(
        'SELECT id, name, slug
         FROM categories
         WHERE deleted_at IS NULL
         ORDER BY id DESC'
    );

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) {
    error_log('Admin categories error: ' . $exception->getMessage());

    $categoriesAvailable = false;
    $errorMessage = 'دریافت دسته‌بندی‌ها ممکن نشد. ساختار جدول categories را بررسی کنید.';
}

$totalCategories = count($categories);

/*
|--------------------------------------------------------------------------
| تنظیمات سایدبار
|--------------------------------------------------------------------------
*/

$activePage = 'categories';

/*
 * فایل سایدبار فعلاً تعداد مقالات را نمایش می‌دهد.
 * در این صفحه به دلیل نداشتن کوئری مقالات، مقداری ارسال نمی‌کنیم.
 */
$sidebarArticleCount = null;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>دسته‌بندی‌ها | پنل مدیریت</title>

    <link rel="stylesheet" href="assets/admin.css">

    <style>
        .category-table-wrapper {
            overflow-x: auto;
        }

        .category-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 620px;
        }

        .category-table th,
        .category-table td {
            padding: 16px 18px;
            text-align: right;
            border-bottom: 1px solid #edf0f5;
            white-space: nowrap;
        }

        .category-table th {
            color: #7b8495;
            font-size: 13px;
            font-weight: 700;
            background: #fafbfc;
        }

        .category-table td {
            color: #303746;
            font-size: 14px;
        }

        .category-table tr:last-child td {
            border-bottom: 0;
        }

        .category-name {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }

        .category-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6654d9;
            background: #efedff;
            font-size: 18px;
        }

        .category-slug {
            direction: ltr;
            text-align: right;
            color: #8a93a3;
            font-family: monospace;
        }

        .category-number {
            color: #7d8798;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 70px;
            height: 34px;
            padding: 0 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            color: #6654d9;
            background: #f0eeff;
        }

        .action-button:hover {
            background: #e5e1ff;
        }

        .empty-state {
            padding: 55px 20px;
            text-align: center;
            color: #7d8798;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            color: #6654d9;
            background: #efedff;
            font-size: 28px;
        }

        .empty-state h3 {
            margin: 0 0 8px;
            color: #303746;
            font-size: 17px;
        }

        .empty-state p {
            margin: 0;
            font-size: 14px;
        }

        .page-heading-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        @media (max-width: 700px) {
            .page-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-heading-actions {
                width: 100%;
            }

            .page-heading-actions .primary-button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
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
                    <strong>دسته‌بندی‌ها</strong>
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
                    <h1>دسته‌بندی‌ها</h1>

                    <p>
                        دسته‌بندی‌های محتوای سایت را از این بخش مدیریت کنید.
                    </p>
                </div>

                <div class="page-heading-actions">
                    <a class="primary-button" href="category-create.php">
                        <span>＋</span>
                        افزودن دسته‌بندی
                    </a>
                </div>
            </div>

            <section class="stats-grid" aria-label="آمار دسته‌بندی‌ها">

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">کل دسته‌بندی‌ها</span>
                        <span class="stat-icon purple">◈</span>
                    </div>

                    <div class="stat-number">
                        <?= $categoriesAvailable ? $totalCategories : '—' ?>
                    </div>

                    <div class="stat-note">
                        تعداد دسته‌بندی‌های فعال سایت
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">مدیریت محتوا</span>
                        <span class="stat-icon green">✓</span>
                    </div>

                    <div class="stat-number">
                        ساده
                    </div>

                    <div class="stat-note">
                        سامان‌دهی بهتر مقالات
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">دسترسی سریع</span>
                        <span class="stat-icon orange">⌁</span>
                    </div>

                    <div class="stat-number">
                        فعال
                    </div>

                    <div class="stat-note">
                        ایجاد و ویرایش دسته‌بندی
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">وضعیت پنل</span>
                        <span class="stat-icon red">●</span>
                    </div>

                    <div class="stat-number">
                        آنلاین
                    </div>

                    <div class="stat-note">
                        سیستم مدیریت آماده استفاده است
                    </div>
                </article>

            </section>

            <section class="dashboard-grid">

                <div class="panel" style="grid-column: 1 / -1;">

                    <div class="panel-heading">
                        <div>
                            <h2>فهرست دسته‌بندی‌ها</h2>
                            <span>مشاهده و مدیریت موضوعات سایت</span>
                        </div>

                        <a class="secondary-button" href="category-create.php">
                            افزودن دسته‌بندی
                        </a>
                    </div>

                    <?php if (!$categoriesAvailable): ?>

                        <div class="notice">
                            <?= e($errorMessage) ?>
                        </div>

                    <?php elseif ($totalCategories === 0): ?>

                        <div class="empty-state">
                            <div class="empty-state-icon">◈</div>

                            <h3>
                                هنوز دسته‌بندی‌ای ایجاد نشده است
                            </h3>

                            <p>
                                برای شروع، اولین دسته‌بندی سایت را ایجاد کنید.
                            </p>

                            <br>

                            <a
                                class="primary-button"
                                href="category-create.php"
                            >
                                <span>＋</span>
                                ایجاد اولین دسته‌بندی
                            </a>
                        </div>

                    <?php else: ?>

                        <div class="category-table-wrapper">
                            <table class="category-table">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>نام دسته‌بندی</th>
                                    <th>نامک</th>
                                    <th>شناسه</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($categories as $index => $category): ?>
                                    <tr>
                                        <td class="category-number">
                                            <?= $index + 1 ?>
                                        </td>

                                        <td>
                                            <div class="category-name">
                                                <span class="category-icon">
                                                    ◈
                                                </span>

                                                <span>
                                                    <?= e((string) $category['name']) ?>
                                                </span>
                                            </div>
                                        </td>

                                        <td class="category-slug">
                                            <?= e((string) ($category['slug'] ?? '—')) ?>
                                        </td>

                                        <td class="category-number">
                                            #<?= (int) $category['id'] ?>
                                        </td>

                                        <td>
                                            <a
                                                class="action-button"
                                                href="category-edit.php?id=<?= (int) $category['id'] ?>"
                                            >
                                                ویرایش
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>

                </div>

            </section>

        </main>
    </div>
</div>

</body>
</html>