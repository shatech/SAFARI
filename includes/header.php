<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#102c3a">

    <?php if (!empty($pageUrl)): ?>
        <link rel="canonical" href="<?= e($pageUrl) ?>">
    <?php endif; ?>

    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <?php if (!empty($pageUrl)): ?>
        <meta property="og:url" content="<?= e($pageUrl) ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
<a class="skip-link" href="#main-content">رفتن به محتوای اصلی</a>

<header class="site-header">
    <nav class="navbar navbar-expand-lg" aria-label="منوی اصلی">
        <div class="container">
            <a class="navbar-brand brand" href="/" aria-label="صفری سازه، صفحه اصلی">
                <span class="brand-symbol" aria-hidden="true">
                    <i class="bi bi-buildings"></i>
                </span>
                <span class="brand-text">
                    <strong>صفری سازه</strong>
                    <small>راهکارهای نوین ساختمان</small>
                </span>
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="باز و بسته کردن منو"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-3 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/#home">صفحه اصلی</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#products">محصولات</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#about">درباره ما</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#process">نحوه همکاری</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#contact">تماس با ما</a></li>
                </ul>

                <a class="header-cta" href="/#contact">
                    درخواست مشاوره
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </nav>
</header>