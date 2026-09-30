<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| متغیرهای مورد انتظار
|--------------------------------------------------------------------------
| $admin
| $activePage
| $sidebarArticleCount اختیاری است
*/

$activePage = $activePage ?? '';
$sidebarArticleCount = $sidebarArticleCount ?? null;

$isActive = static function (string $page) use ($activePage): string {
    return $activePage === $page ? ' active' : '';
};
?>

<aside class="sidebar">
    <div class="brand">
        <div class="brand-mark">ن</div>

        <div>
            <div class="brand-name">مدیریت محتوا</div>
            <div class="brand-caption">پنل مدیریت وب‌سایت</div>
        </div>
    </div>

    <label class="sidebar-close" for="nav-toggle" aria-label="بستن منو">
        ×
    </label>

    <div class="nav-caption">منوی اصلی</div>

    <nav class="side-nav">
        <a class="side-link<?= $isActive('dashboard') ?>" href="index.php">
            <span class="side-icon">▦</span>
            <span>داشبورد</span>
        </a>

        <a class="side-link<?= $isActive('articles') ?>" href="articles.php">
            <span class="side-icon">▤</span>
            <span>مقالات</span>

            <?php if ($sidebarArticleCount !== null): ?>
                <small><?= (int) $sidebarArticleCount ?></small>
            <?php endif; ?>
        </a>

        <a class="side-link<?= $isActive('categories') ?>" href="categories.php">
            <span class="side-icon">◈</span>
            <span>دسته‌بندی‌ها</span>
        </a>

        <a class="side-link<?= $isActive('users') ?>" href="users.php">
            <span class="side-icon">♙</span>
            <span>کاربران</span>
        </a>
    </nav>

    <div class="nav-caption settings-caption">
        تنظیمات
    </div>

    <nav class="side-nav">
        <a class="side-link<?= $isActive('settings') ?>" href="settings.php">
            <span class="side-icon">⚙</span>
            <span>تنظیمات سایت</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <div class="help-card">
            <strong>پنل مدیریت</strong>
            <p>
                از این بخش محتوای سایت و تنظیمات آن را مدیریت کنید.
            </p>
        </div>
    </div>
</aside>