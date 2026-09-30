<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$admin = require_admin();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| توابع کمکی
|--------------------------------------------------------------------------
*/

function category_slug(string $value): string
{
    $value = trim($value);

    // یکسان‌سازی حروف عربی و فارسی
    $value = str_replace(
        ['ي', 'ى', 'ك', 'ة', 'ۀ', 'ؤ', 'إ', 'أ', 'آ'],
        ['ی', 'ی', 'ک', 'ه', 'ۀ', 'و', 'ا', 'ا', 'آ'],
        $value
    );

    // تبدیل فاصله و کاراکترهای جداکننده به خط تیره
    $value = preg_replace('/[\s_]+/u', '-', $value) ?? $value;

    // حذف کاراکترهای غیرمجاز
    $value = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $value) ?? $value;

    // حذف خط‌تیره‌های پشت سر هم
    $value = preg_replace('/-+/u', '-', $value) ?? $value;

    return trim($value, '-');
}

/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

/*
|--------------------------------------------------------------------------
| متغیرهای فرم
|--------------------------------------------------------------------------
*/

$name = '';
$slug = '';
$errors = [];
$successMessage = '';

/*
|--------------------------------------------------------------------------
| ثبت دسته‌بندی
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string) ($_POST['csrf_token'] ?? '');

    if (
        $postedToken === '' ||
        !hash_equals($csrfToken, $postedToken)
    ) {
        $errors[] = 'درخواست نامعتبر است. لطفاً صفحه را دوباره بارگذاری کنید.';
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = trim((string) ($_POST['slug'] ?? ''));

    if ($name === '') {
        $errors[] = 'نام دسته‌بندی را وارد کنید.';
    } elseif (mb_strlen($name, 'UTF-8') < 2) {
        $errors[] = 'نام دسته‌بندی باید حداقل ۲ کاراکتر باشد.';
    } elseif (mb_strlen($name, 'UTF-8') > 120) {
        $errors[] = 'نام دسته‌بندی نمی‌تواند بیشتر از ۱۲۰ کاراکتر باشد.';
    }

    /*
     * اگر نامک خالی باشد، به‌صورت خودکار از روی نام دسته‌بندی ساخته می‌شود.
     */
    if ($slug === '') {
        $slug = category_slug($name);
    } else {
        $slug = category_slug($slug);
    }

    if ($slug === '') {
        $errors[] = 'نامک دسته‌بندی معتبر نیست.';
    } elseif (mb_strlen($slug, 'UTF-8') > 160) {
        $errors[] = 'نامک دسته‌بندی نمی‌تواند بیشتر از ۱۶۰ کاراکتر باشد.';
    }

    if (!$errors) {
        try {
            /*
             * بررسی دسته‌بندی تکراری
             */
            $duplicateStmt = db()->prepare(
                'SELECT id
                 FROM categories
                 WHERE deleted_at IS NULL
                   AND (name = :name OR slug = :slug)
                 LIMIT 1'
            );

            $duplicateStmt->execute([
                'name' => $name,
                'slug' => $slug,
            ]);

            if ($duplicateStmt->fetch(PDO::FETCH_ASSOC)) {
                $errors[] = 'دسته‌بندی یا نامک واردشده قبلاً ثبت شده است.';
            } else {
                $insertStmt = db()->prepare(
                    'INSERT INTO categories (name, slug)
                     VALUES (:name, :slug)'
                );

                $insertStmt->execute([
                    'name' => $name,
                    'slug' => $slug,
                ]);

                header('Location: categories.php?created=1');
                exit;
            }
        } catch (Throwable $exception) {
            error_log('Admin category create error: ' . $exception->getMessage());

            $errors[] = 'ثبت دسته‌بندی انجام نشد. ساختار جدول categories را بررسی کنید.';
        }
    }
}

/*
|--------------------------------------------------------------------------
| تنظیمات سایدبار
|--------------------------------------------------------------------------
*/

$activePage = 'categories';
$sidebarArticleCount = null;

?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>افزودن دسته‌بندی | پنل مدیریت</title>

    <link rel="stylesheet" href="assets/admin.css">

    <style>
        .form-panel {
            max-width: 850px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            margin-bottom: 9px;
            color: #303746;
            font-size: 14px;
            font-weight: 700;
        }

        .form-label span {
            color: #e05252;
        }

        .form-control {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1px solid #e3e7ef;
            border-radius: 10px;
            outline: none;
            background: #fff;
            color: #303746;
            font-family: inherit;
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s;
            box-sizing: border-box;
        }

        textarea.form-control {
            height: auto;
            min-height: 120px;
            padding-top: 13px;
            resize: vertical;
        }

        .form-control:focus {
            border-color: #6654d9;
            box-shadow: 0 0 0 3px rgba(102, 84, 217, .12);
        }

        .form-help {
            display: block;
            margin-top: 8px;
            color: #8992a3;
            font-size: 12px;
            line-height: 1.8;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 8px;
        }

        .secondary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 9px;
            color: #596273;
            background: #f0f2f6;
            text-decoration: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .secondary-button:hover {
            background: #e6e9ef;
        }

        .form-errors {
            margin-bottom: 22px;
            padding: 13px 16px;
            border: 1px solid #ffd7d7;
            border-radius: 10px;
            color: #a13e3e;
            background: #fff3f3;
            font-size: 13px;
            line-height: 2;
        }

        .form-errors ul {
            margin: 0;
            padding-right: 18px;
        }

        .slug-preview {
            direction: ltr;
            text-align: left;
        }

        @media (max-width: 600px) {
            .form-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .form-actions .primary-button,
            .form-actions .secondary-button {
                width: 100%;
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
                    <a href="categories.php">دسته‌بندی‌ها</a>
                    <span> / </span>
                    <strong>افزودن دسته‌بندی</strong>
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
                    <h1>افزودن دسته‌بندی</h1>

                    <p>
                        یک دسته‌بندی جدید برای سامان‌دهی مقالات ایجاد کنید.
                    </p>
                </div>

                <a class="secondary-button" href="categories.php">
                    بازگشت به دسته‌بندی‌ها
                </a>
            </div>

            <section class="dashboard-grid">

                <div class="panel form-panel">

                    <div class="panel-heading">
                        <div>
                            <h2>اطلاعات دسته‌بندی</h2>
                            <span>اطلاعات موردنیاز را وارد کنید</span>
                        </div>
                    </div>

                    <?php if ($errors): ?>
                        <div class="form-errors">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="category-create.php">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($csrfToken) ?>"
                        >

                        <div class="form-group">
                            <label class="form-label" for="name">
                                نام دسته‌بندی
                                <span>*</span>
                            </label>

                            <input
                                class="form-control"
                                type="text"
                                id="name"
                                name="name"
                                value="<?= e($name) ?>"
                                placeholder="مثلاً: اخبار فناوری"
                                maxlength="120"
                                required
                                autofocus
                            >

                            <small class="form-help">
                                نامی کوتاه و واضح برای موضوع دسته‌بندی وارد کنید.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="slug">
                                نامک دسته‌بندی
                            </label>

                            <input
                                class="form-control slug-preview"
                                type="text"
                                id="slug"
                                name="slug"
                                value="<?= e($slug) ?>"
                                placeholder="مثلاً: اخبار-فناوری"
                                maxlength="160"
                            >

                            <small class="form-help">
                                نامک برای آدرس اینترنتی استفاده می‌شود.
                                در صورت خالی‌بودن، به‌صورت خودکار ساخته خواهد شد.
                            </small>
                        </div>

                        <div class="form-actions">
                            <button class="primary-button" type="submit">
                                <span>✓</span>
                                ذخیره دسته‌بندی
                            </button>

                            <a class="secondary-button" href="categories.php">
                                انصراف
                            </a>
                        </div>

                    </form>

                </div>

            </section>

        </main>
    </div>
</div>

<script>
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    function makeSlug(value) {
        return value
            .trim()
            .replace(/[يى]/g, 'ی')
            .replace(/ك/g, 'ک')
            .replace(/\s+/g, '-')
            .replace(/[^\u0600-\u06FFa-zA-Z0-9-]/g, '')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    let slugManuallyChanged = slugInput.value.trim() !== '';

    slugInput.addEventListener('input', function () {
        slugManuallyChanged = true;
    });

    nameInput.addEventListener('input', function () {
        if (!slugManuallyChanged) {
            slugInput.value = makeSlug(nameInput.value);
        }
    });
</script>

</body>
</html>