<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$admin = require_admin();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| تنظیمات
|--------------------------------------------------------------------------
*/

$activePage = 'articles';

$articles = [];
$articlesAvailable = true;
$errorMessage = '';

$search = trim((string) ($_GET['q'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$totalArticles = 0;
$filteredCount = 0;
$totalPages = 1;
$publishedCount = 0;
$draftCount = 0;
$scheduledCount = 0;
$archivedCount = 0;

/*
|--------------------------------------------------------------------------
| وضعیت‌های معتبر جدول articles
|--------------------------------------------------------------------------
*/

$allowedStatuses = [
    'draft',
    'scheduled',
    'published',
    'archived',
];

/*
|--------------------------------------------------------------------------
| شروط جست‌وجو
|--------------------------------------------------------------------------
*/

$where = [
    'a.deleted_at IS NULL',
];

$params = [];

if ($search !== '') {
    $where[] = '(
        a.title LIKE :search_title
        OR a.slug LIKE :search_slug
        OR a.focus_keyword LIKE :search_keyword
        OR a.meta_description LIKE :search_meta
    )';

    $searchValue = '%' . $search . '%';

    $params['search_title'] = $searchValue;
    $params['search_slug'] = $searchValue;
    $params['search_keyword'] = $searchValue;
    $params['search_meta'] = $searchValue;
}

if ($status !== '' && in_array($status, $allowedStatuses, true)) {
    $where[] = 'a.status = :status';
    $params['status'] = $status;
} else {
    $status = '';
}

$whereSql = implode(' AND ', $where);

/*
|--------------------------------------------------------------------------
| آمار مقالات
|--------------------------------------------------------------------------
*/

try {
    $statsStmt = db()->query(
        "SELECT
            COUNT(*) AS total_count,

            SUM(
                CASE
                    WHEN status = 'published' THEN 1
                    ELSE 0
                END
            ) AS published_count,

            SUM(
                CASE
                    WHEN status = 'draft' THEN 1
                    ELSE 0
                END
            ) AS draft_count,

            SUM(
                CASE
                    WHEN status = 'scheduled' THEN 1
                    ELSE 0
                END
            ) AS scheduled_count,

            SUM(
                CASE
                    WHEN status = 'archived' THEN 1
                    ELSE 0
                END
            ) AS archived_count

         FROM articles
         WHERE deleted_at IS NULL"
    );

    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $totalArticles = (int) ($stats['total_count'] ?? 0);
    $publishedCount = (int) ($stats['published_count'] ?? 0);
    $draftCount = (int) ($stats['draft_count'] ?? 0);
    $scheduledCount = (int) ($stats['scheduled_count'] ?? 0);
    $archivedCount = (int) ($stats['archived_count'] ?? 0);
} catch (Throwable $exception) {
    error_log('Articles stats error: ' . $exception->getMessage());

    $articlesAvailable = false;
    $errorMessage = 'دریافت آمار مقالات انجام نشد.';
}

/*
|--------------------------------------------------------------------------
| تعداد مقالات فیلترشده
|--------------------------------------------------------------------------
*/

if ($articlesAvailable) {
    try {
        $countStmt = db()->prepare(
            "SELECT COUNT(*)
             FROM articles a
             WHERE {$whereSql}"
        );

        $countStmt->execute($params);

        $filteredCount = (int) $countStmt->fetchColumn();

        $totalPages = max(
            1,
            (int) ceil($filteredCount / $perPage)
        );

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }
    } catch (Throwable $exception) {
        error_log('Articles count error: ' . $exception->getMessage());

        $articlesAvailable = false;
        $errorMessage = 'تعداد مقالات دریافت نشد.';
    }
}

/*
|--------------------------------------------------------------------------
| دریافت فهرست مقالات
|--------------------------------------------------------------------------
|
| این کوئری فقط از ستون‌های موجود در جدول articles استفاده می‌کند.
| category_id و جدول categories عمداً استفاده نشده‌اند.
|--------------------------------------------------------------------------
*/

if ($articlesAvailable) {
    try {
        $articleStmt = db()->prepare(
            "SELECT
                a.id,
                a.author_id,
                a.title,
                a.slug,
                a.excerpt,
                a.body,
                a.status,
                a.published_at,
                a.cover_image,
                a.cover_alt,
                a.seo_title,
                a.meta_description,
                a.canonical_url,
                a.robots_index,
                a.robots_follow,
                a.focus_keyword,
                a.created_at,
                a.updated_at
             FROM articles a
             WHERE {$whereSql}
             ORDER BY
                CASE
                    WHEN a.status = 'published' THEN 1
                    WHEN a.status = 'scheduled' THEN 2
                    WHEN a.status = 'draft' THEN 3
                    WHEN a.status = 'archived' THEN 4
                    ELSE 5
                END,
                a.created_at DESC,
                a.id DESC
             LIMIT {$perPage}
             OFFSET {$offset}"
        );

        $articleStmt->execute($params);

        $articles = $articleStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $exception) {
        error_log('Articles list error: ' . $exception->getMessage());

        $articlesAvailable = false;
        $errorMessage = 'دریافت فهرست مقالات انجام نشد.';
    }
}

/*
|--------------------------------------------------------------------------
| توابع نمایشی
|--------------------------------------------------------------------------
*/

function article_status_label(string $status): string
{
    return match ($status) {
        'published' => 'منتشرشده',
        'draft' => 'پیش‌نویس',
        'scheduled' => 'زمان‌بندی‌شده',
        'archived' => 'بایگانی‌شده',
        default => 'نامشخص',
    };
}

function article_status_class(string $status): string
{
    return match ($status) {
        'published' => 'status-published',
        'draft' => 'status-draft',
        'scheduled' => 'status-scheduled',
        'archived' => 'status-archived',
        default => 'status-unknown',
    };
}

function article_seo_score(array $article): int
{
    $score = 0;

    $title = trim((string) ($article['title'] ?? ''));
    $slug = trim((string) ($article['slug'] ?? ''));
    $seoTitle = trim((string) ($article['seo_title'] ?? ''));
    $metaDescription = trim(
        (string) ($article['meta_description'] ?? '')
    );
    $focusKeyword = trim(
        (string) ($article['focus_keyword'] ?? '')
    );
    $coverImage = trim(
        (string) ($article['cover_image'] ?? '')
    );

    if ($title !== '') {
        $score += 20;
    }

    if ($slug !== '') {
        $score += 20;
    }

    if ($seoTitle !== '') {
        $score += 20;
    }

    if ($metaDescription !== '') {
        $score += 20;
    }

    if ($focusKeyword !== '') {
        $score += 10;
    }

    if ($coverImage !== '') {
        $score += 10;
    }

    return min(100, $score);
}

function article_seo_label(int $score): string
{
    if ($score >= 80) {
        return 'عالی';
    }

    if ($score >= 50) {
        return 'متوسط';
    }

    return 'نیازمند بهبود';
}

function article_seo_class(int $score): string
{
    if ($score >= 80) {
        return 'seo-good';
    }

    if ($score >= 50) {
        return 'seo-medium';
    }

    return 'seo-poor';
}

function article_date(?string $date): string
{
    if (!$date) {
        return '—';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return $date;
    }

    return date('Y/m/d H:i', $timestamp);
}

function query_url(array $changes = []): string
{
    $query = $_GET;

    foreach ($changes as $key => $value) {
        if ($value === null || $value === '') {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }
    }

    $query['page'] = $query['page'] ?? 1;

    return '?' . http_build_query($query);
}

?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>مقالات | پنل مدیریت</title>

    <meta
        name="description"
        content="مدیریت مقالات سایت"
    >

    <link rel="stylesheet" href="assets/admin.css">

    <style>
        .articles-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .filter-form {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            width: 100%;
            flex-wrap: wrap;
        }

        .filter-field {
            min-width: 180px;
        }

        .filter-field.search-field {
            flex: 1;
            min-width: 260px;
        }

        .filter-label {
            display: block;
            margin-bottom: 7px;
            color: #7b8495;
            font-size: 12px;
            font-weight: 700;
        }

        .filter-control {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #e3e7ef;
            border-radius: 9px;
            outline: none;
            background: #fff;
            color: #303746;
            font-family: inherit;
            font-size: 13px;
            box-sizing: border-box;
        }

        .filter-control:focus {
            border-color: #6654d9;
            box-shadow: 0 0 0 3px rgba(102, 84, 217, .10);
        }

        .filter-submit,
        .clear-filters {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 17px;
            border: 0;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .filter-submit {
            color: #fff;
            background: #6654d9;
        }

        .filter-submit:hover {
            background: #5745c6;
        }

        .clear-filters {
            color: #687386;
            background: #f2f4f7;
        }

        .clear-filters:hover {
            background: #e8ebf0;
        }

        .article-table-wrapper {
            overflow-x: auto;
        }

        .article-table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        .article-table th,
        .article-table td {
            padding: 15px 16px;
            text-align: right;
            border-bottom: 1px solid #edf0f5;
            white-space: nowrap;
        }

        .article-table th {
            color: #7b8495;
            background: #fafbfc;
            font-size: 12px;
            font-weight: 800;
        }

        .article-table td {
            color: #303746;
            font-size: 13px;
        }

        .article-table tr:last-child td {
            border-bottom: 0;
        }

        .article-title-cell {
            min-width: 280px;
            max-width: 390px;
        }

        .article-title {
            display: block;
            overflow: hidden;
            color: #303746;
            font-weight: 800;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .article-excerpt {
            display: block;
            max-width: 370px;
            overflow: hidden;
            margin-top: 6px;
            color: #939baa;
            font-size: 11px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .article-slug {
            display: block;
            max-width: 300px;
            overflow: hidden;
            margin-top: 5px;
            direction: ltr;
            color: #9aa2af;
            font-family: monospace;
            text-align: right;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .status-badge,
        .seo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 800;
        }

        .status-published {
            color: #218653;
            background: #e8f8ef;
        }

        .status-draft {
            color: #a56b14;
            background: #fff4df;
        }

        .status-scheduled {
            color: #2873a8;
            background: #e8f4fc;
        }

        .status-archived {
            color: #707987;
            background: #eef0f3;
        }

        .status-unknown {
            color: #c24c4c;
            background: #fff0f0;
        }

        .seo-good {
            color: #218653;
            background: #e8f8ef;
        }

        .seo-medium {
            color: #a56b14;
            background: #fff4df;
        }

        .seo-poor {
            color: #c24c4c;
            background: #fff0f0;
        }

        .seo-score {
            display: block;
            margin-top: 5px;
            color: #929baa;
            font-size: 11px;
        }

        .article-date {
            color: #7d8798;
            direction: ltr;
            font-size: 12px;
            text-align: right;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .table-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 58px;
            height: 32px;
            padding: 0 9px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
        }

        .edit-action {
            color: #6654d9;
            background: #f0eeff;
        }

        .edit-action:hover {
            background: #e5e1ff;
        }

        .view-action {
            color: #2873a8;
            background: #e8f4fc;
        }

        .view-action:hover {
            background: #dceefa;
        }

        .empty-state {
            padding: 58px 20px;
            color: #7d8798;
            text-align: center;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
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
            margin: 0 0 20px;
            font-size: 14px;
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 20px;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #8992a3;
            font-size: 12px;
        }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 9px;
            border-radius: 7px;
            color: #657084;
            background: #f1f3f6;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
        }

        .pagination-link:hover,
        .pagination-link.active {
            color: #fff;
            background: #6654d9;
        }

        .pagination-link.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .notice-success {
            margin-bottom: 20px;
            padding: 13px 16px;
            border: 1px solid #c9efd9;
            border-radius: 10px;
            color: #218653;
            background: #effcf4;
            font-size: 13px;
        }

        .notice-error {
            padding: 15px 17px;
            border-radius: 10px;
            color: #a13e3e;
            background: #fff3f3;
            font-size: 13px;
        }

        @media (max-width: 700px) {
            .filter-form {
                align-items: stretch;
                flex-direction: column;
            }

            .filter-field,
            .filter-field.search-field {
                width: 100%;
                min-width: 0;
            }

            .filter-submit,
            .clear-filters {
                width: 100%;
            }

            .pagination {
                align-items: stretch;
                flex-direction: column;
            }

            .pagination-links {
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
                    <strong>مقالات</strong>
                </div>

            </div>

            <div class="topbar-actions">
                <div class="profile">

                    <div class="avatar">
                        <?= e(mb_substr(
                            (string) ($admin['name'] ?? 'م'),
                            0,
                            1,
                            'UTF-8'
                        )) ?>
                    </div>

                    <div>
                        <div class="profile-name">
                            <?= e((string) ($admin['name'] ?? 'مدیر')) ?>
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
                    <h1>مقالات</h1>

                    <p>
                        مقالات سایت را مدیریت، ویرایش و منتشر کنید.
                    </p>
                </div>

                <a class="primary-button" href="article-create.php">
                    <span>＋</span>
                    افزودن مقاله
                </a>

            </div>

            <?php if (isset($_GET['created'])): ?>
                <div class="notice-success">
                    مقاله با موفقیت ایجاد شد.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['updated'])): ?>
                <div class="notice-success">
                    مقاله با موفقیت ویرایش شد.
                </div>
            <?php endif; ?>

            <section class="stats-grid" aria-label="آمار مقالات">

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">کل مقالات</span>
                        <span class="stat-icon purple">▤</span>
                    </div>

                    <div class="stat-number">
                        <?= $articlesAvailable ? $totalArticles : '—' ?>
                    </div>

                    <div class="stat-note">
                        مقالات فعال سایت
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">منتشرشده</span>
                        <span class="stat-icon green">✓</span>
                    </div>

                    <div class="stat-number">
                        <?= $articlesAvailable ? $publishedCount : '—' ?>
                    </div>

                    <div class="stat-note">
                        قابل مشاهده برای کاربران
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">پیش‌نویس‌ها</span>
                        <span class="stat-icon orange">✎</span>
                    </div>

                    <div class="stat-number">
                        <?= $articlesAvailable ? $draftCount : '—' ?>
                    </div>

                    <div class="stat-note">
                        مقالات در حال آماده‌سازی
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">زمان‌بندی‌شده</span>
                        <span class="stat-icon red">◷</span>
                    </div>

                    <div class="stat-number">
                        <?= $articlesAvailable ? $scheduledCount : '—' ?>
                    </div>

                    <div class="stat-note">
                        مقالات زمان‌بندی‌شده
                    </div>
                </article>

            </section>

            <section class="panel">

                <div class="panel-heading">
                    <div>
                        <h2>فهرست مقالات</h2>

                        <span>
                            <?= $filteredCount ?>
                            نتیجه برای مدیریت
                        </span>
                    </div>
                </div>

                <div class="articles-toolbar">

                    <form
                        class="filter-form"
                        method="get"
                        action="articles.php"
                    >

                        <div class="filter-field search-field">
                            <label
                                class="filter-label"
                                for="q"
                            >
                                جست‌وجو
                            </label>

                            <input
                                class="filter-control"
                                type="search"
                                id="q"
                                name="q"
                                value="<?= e($search) ?>"
                                placeholder="عنوان، نامک یا کلمه کلیدی..."
                            >
                        </div>

                        <div class="filter-field">
                            <label
                                class="filter-label"
                                for="status"
                            >
                                وضعیت
                            </label>

                            <select
                                class="filter-control"
                                id="status"
                                name="status"
                            >
                                <option value="">
                                    همه وضعیت‌ها
                                </option>

                                <?php foreach ($allowedStatuses as $itemStatus): ?>
                                    <option
                                        value="<?= e($itemStatus) ?>"
                                        <?= $status === $itemStatus
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= e(article_status_label($itemStatus)) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <button
                            class="filter-submit"
                            type="submit"
                        >
                            جست‌وجو
                        </button>

                        <?php if ($search !== '' || $status !== ''): ?>
                            <a
                                class="clear-filters"
                                href="articles.php"
                            >
                                حذف فیلترها
                            </a>
                        <?php endif; ?>

                    </form>

                </div>

                <?php if (!$articlesAvailable): ?>

                    <div class="notice-error">
                        <?= e($errorMessage) ?>
                    </div>

                <?php elseif (!$articles): ?>

                    <div class="empty-state">

                        <div class="empty-state-icon">
                            ▤
                        </div>

                        <h3>
                            مقاله‌ای پیدا نشد
                        </h3>

                        <p>
                            هنوز مقاله‌ای ایجاد نشده یا نتیجه‌ای با فیلترهای فعلی وجود ندارد.
                        </p>

                        <?php if ($search === '' && $status === ''): ?>
                            <a
                                class="primary-button"
                                href="article-create.php"
                            >
                                <span>＋</span>
                                ایجاد اولین مقاله
                            </a>
                        <?php else: ?>
                            <a
                                class="secondary-button"
                                href="articles.php"
                            >
                                نمایش همه مقالات
                            </a>
                        <?php endif; ?>

                    </div>

                <?php else: ?>

                    <div class="article-table-wrapper">

                        <table class="article-table">

                            <thead>
                            <tr>
                                <th>مقاله</th>
                                <th>وضعیت</th>
                                <th>سئو</th>
                                <th>تاریخ ایجاد</th>
                                <th>آخرین ویرایش</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>

                            <tbody>

                            <?php foreach ($articles as $article): ?>

                                <?php
                                $articleStatus = (string) (
                                    $article['status'] ?? ''
                                );

                                $seoScore = article_seo_score($article);
                                ?>

                                <tr>

                                    <td class="article-title-cell">

                                        <span class="article-title">
                                            <?= e((string) (
                                                $article['title'] ?? 'بدون عنوان'
                                            )) ?>
                                        </span>

                                        <?php if (
                                            trim((string) (
                                                $article['excerpt'] ?? ''
                                            )) !== ''
                                        ): ?>
                                            <span class="article-excerpt">
                                                <?= e((string) (
                                                    $article['excerpt'] ?? ''
                                                )) ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="article-slug">
                                            /<?= e((string) (
                                                $article['slug'] ?? ''
                                            )) ?>
                                        </span>

                                    </td>

                                    <td>
                                        <span class="status-badge <?= e(
                                            article_status_class($articleStatus)
                                        ) ?>">
                                            <?= e(
                                                article_status_label(
                                                    $articleStatus
                                                )
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>

                                        <span class="seo-badge <?= e(
                                            article_seo_class($seoScore)
                                        ) ?>">
                                            <?= e(
                                                article_seo_label($seoScore)
                                            ) ?>
                                        </span>

                                        <span class="seo-score">
                                            <?= $seoScore ?> از ۱۰۰
                                        </span>

                                    </td>

                                    <td class="article-date">
                                        <?= e(article_date(
                                            $article['created_at'] ?? null
                                        )) ?>
                                    </td>

                                    <td class="article-date">
                                        <?= e(article_date(
                                            $article['updated_at'] ?? null
                                        )) ?>
                                    </td>

                                    <td>

                                        <div class="table-actions">

                                            <a
                                                class="table-action edit-action"
                                                href="article-edit.php?id=<?= (int) (
                                                    $article['id'] ?? 0
                                                ) ?>"
                                            >
                                                ویرایش
                                            </a>

                                            <?php if (
                                                $articleStatus === 'published'
                                            ): ?>
                                                <a
                                                    class="table-action view-action"
                                                    href="../article.php?slug=<?= urlencode(
                                                        (string) (
                                                            $article['slug'] ?? ''
                                                        )
                                                    ) ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    مشاهده
                                                </a>
                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <?php if ($totalPages > 1): ?>

                        <div class="pagination">

                            <div class="pagination-info">
                                صفحه <?= $page ?> از <?= $totalPages ?>
                            </div>

                            <div class="pagination-links">

                                <?php if ($page > 1): ?>

                                    <a
                                        class="pagination-link"
                                        href="<?= e(query_url([
                                            'page' => $page - 1,
                                        ])) ?>"
                                    >
                                        قبلی
                                    </a>

                                <?php else: ?>

                                    <span class="pagination-link disabled">
                                        قبلی
                                    </span>

                                <?php endif; ?>

                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                ?>

                                <?php for (
                                    $pageNumber = $startPage;
                                    $pageNumber <= $endPage;
                                    $pageNumber++
                                ): ?>

                                    <a
                                        class="pagination-link <?= $pageNumber === $page
                                            ? 'active'
                                            : '' ?>"
                                        href="<?= e(query_url([
                                            'page' => $pageNumber,
                                        ])) ?>"
                                    >
                                        <?= $pageNumber ?>
                                    </a>

                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>

                                    <a
                                        class="pagination-link"
                                        href="<?= e(query_url([
                                            'page' => $page + 1,
                                        ])) ?>"
                                    >
                                        بعدی
                                    </a>

                                <?php else: ?>

                                    <span class="pagination-link disabled">
                                        بعدی
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                <?php endif; ?>

            </section>

        </main>

    </div>

</div>

</body>
</html>