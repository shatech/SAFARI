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

function ae_escape(mixed $value): string
{
    if (function_exists('e')) {
        return e((string) $value);
    }

    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function ae_slug(string $value): string
{
    $value = trim($value);

    $value = str_replace(
        ['ي', 'ى', 'ك', 'ة', 'ؤ', 'إ', 'أ'],
        ['ی', 'ی', 'ک', 'ه', 'و', 'ا', 'ا'],
        $value
    );

    $value = preg_replace('/[\s_]+/u', '-', $value) ?? $value;
    $value = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $value) ?? $value;
    $value = preg_replace('/-+/u', '-', $value) ?? $value;

    return trim($value, '-');
}

function ae_url(string $value): string
{
    $value = trim($value);

    if ($value === '') {
        return '';
    }

    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return '';
    }

    $scheme = strtolower(
        (string) parse_url($value, PHP_URL_SCHEME)
    );

    if (!in_array($scheme, ['http', 'https'], true)) {
        return '';
    }

    return $value;
}

function ae_html(string $value): string
{
    $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><a><blockquote><code><pre>';

    $value = strip_tags($value, $allowed);

    $value = preg_replace(
        '/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu',
        '',
        $value
    ) ?? $value;

    $value = preg_replace(
        '/javascript\s*:/iu',
        '',
        $value
    ) ?? $value;

    return trim($value);
}

function ae_clean_blocks(array $blocks): array
{
    $allowedTypes = [
        'paragraph',
        'heading',
        'image',
        'gallery',
        'quote',
        'video',
        'button',
        'alert',
        'code',
        'faq',
        'poll',
        'columns',
        'divider',
        'spacer',
    ];

    $result = [];

    foreach ($blocks as $block) {
        if (!is_array($block)) {
            continue;
        }

        $type = (string) ($block['type'] ?? '');

        if (!in_array($type, $allowedTypes, true)) {
            continue;
        }

        $id = preg_replace(
            '/[^a-zA-Z0-9_\-]/',
            '',
            (string) ($block['id'] ?? '')
        );

        if ($id === '') {
            $id = uniqid('block_', true);
        }

        $data = is_array($block['data'] ?? null)
            ? $block['data']
            : [];

        switch ($type) {
            case 'paragraph':
                $data = [
                    'html' => ae_html(
                        (string) ($data['html'] ?? '')
                    ),
                ];
                break;

            case 'heading':
                $level = (string) ($data['level'] ?? 'h2');

                if (!in_array($level, ['h2', 'h3', 'h4'], true)) {
                    $level = 'h2';
                }

                $data = [
                    'level' => $level,
                    'text' => trim(
                        (string) ($data['text'] ?? '')
                    ),
                ];
                break;

            case 'image':
                $data = [
                    'url' => ae_url(
                        (string) ($data['url'] ?? '')
                    ),
                    'alt' => trim(
                        (string) ($data['alt'] ?? '')
                    ),
                    'caption' => trim(
                        (string) ($data['caption'] ?? '')
                    ),
                ];
                break;

            case 'gallery':
                $images = [];

                foreach (($data['images'] ?? []) as $image) {
                    if (!is_array($image)) {
                        continue;
                    }

                    $url = ae_url(
                        (string) ($image['url'] ?? '')
                    );

                    if ($url === '') {
                        continue;
                    }

                    $images[] = [
                        'url' => $url,
                        'alt' => trim(
                            (string) ($image['alt'] ?? '')
                        ),
                    ];
                }

                $data = [
                    'columns' => max(
                        2,
                        min(
                            4,
                            (int) ($data['columns'] ?? 3)
                        )
                    ),
                    'images' => $images,
                ];
                break;

            case 'quote':
                $data = [
                    'text' => trim(
                        (string) ($data['text'] ?? '')
                    ),
                    'author' => trim(
                        (string) ($data['author'] ?? '')
                    ),
                ];
                break;

            case 'video':
                $data = [
                    'url' => ae_url(
                        (string) ($data['url'] ?? '')
                    ),
                    'title' => trim(
                        (string) ($data['title'] ?? '')
                    ),
                ];
                break;

            case 'button':
                $style = (string) ($data['style'] ?? 'primary');
                $align = (string) ($data['align'] ?? 'right');

                if (!in_array(
                    $style,
                    ['primary', 'secondary', 'success'],
                    true
                )) {
                    $style = 'primary';
                }

                if (!in_array(
                    $align,
                    ['right', 'center', 'left'],
                    true
                )) {
                    $align = 'right';
                }

                $data = [
                    'text' => trim(
                        (string) ($data['text'] ?? '')
                    ),
                    'url' => ae_url(
                        (string) ($data['url'] ?? '')
                    ),
                    'style' => $style,
                    'align' => $align,
                ];
                break;

            case 'alert':
                $alertType = (string) (
                    $data['alert_type'] ?? 'info'
                );

                if (!in_array(
                    $alertType,
                    ['info', 'success', 'warning', 'danger'],
                    true
                )) {
                    $alertType = 'info';
                }

                $data = [
                    'title' => trim(
                        (string) ($data['title'] ?? '')
                    ),
                    'text' => trim(
                        (string) ($data['text'] ?? '')
                    ),
                    'alert_type' => $alertType,
                ];
                break;

            case 'code':
                $data = [
                    'language' => trim(
                        (string) ($data['language'] ?? 'text')
                    ),
                    'code' => (string) (
                        $data['code'] ?? ''
                    ),
                ];
                break;

            case 'faq':
                $items = [];

                foreach (($data['items'] ?? []) as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $question = trim(
                        (string) ($item['question'] ?? '')
                    );

                    $answer = trim(
                        (string) ($item['answer'] ?? '')
                    );

                    if ($question === '' && $answer === '') {
                        continue;
                    }

                    $items[] = [
                        'question' => $question,
                        'answer' => ae_html($answer),
                    ];
                }

                $data = [
                    'items' => $items,
                ];
                break;

            case 'poll':
                $options = [];

                foreach (($data['options'] ?? []) as $option) {
                    $option = trim((string) $option);

                    if ($option !== '') {
                        $options[] = $option;
                    }
                }

                $data = [
                    'question' => trim(
                        (string) ($data['question'] ?? '')
                    ),
                    'options' => array_slice($options, 0, 10),
                ];
                break;

            case 'columns':
                $data = [
                    'right' => ae_html(
                        (string) ($data['right'] ?? '')
                    ),
                    'left' => ae_html(
                        (string) ($data['left'] ?? '')
                    ),
                ];
                break;

            case 'divider':
                $style = (string) (
                    $data['style'] ?? 'solid'
                );

                if (!in_array(
                    $style,
                    ['solid', 'dashed', 'dotted'],
                    true
                )) {
                    $style = 'solid';
                }

                $data = [
                    'style' => $style,
                ];
                break;

            case 'spacer':
                $data = [
                    'height' => max(
                        10,
                        min(
                            300,
                            (int) ($data['height'] ?? 40)
                        )
                    ),
                ];
                break;
        }

        $result[] = [
            'id' => $id,
            'type' => $type,
            'data' => $data,
        ];
    }

    return $result;
}

function ae_old(string $key, mixed $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    return ae_escape($value);
}

/*
|--------------------------------------------------------------------------
| دریافت شناسه مقاله
|--------------------------------------------------------------------------
*/

$articleId = (int) (
    $_GET['id']
    ?? $_GET['article_id']
    ?? $_GET['edit']
    ?? $_POST['article_id']
    ?? 0
);

if ($articleId <= 0) {
    header('Location: articles.php');
    exit;
}

$errors = [];
$article = null;
$categories = [];
$tableColumns = [];

/*
|--------------------------------------------------------------------------
| بارگذاری ساختار جدول
|--------------------------------------------------------------------------
*/

try {
    $columnsStatement = db()->query(
        'SHOW COLUMNS FROM articles'
    );

    $columns = $columnsStatement->fetchAll(
        PDO::FETCH_ASSOC
    );

    foreach ($columns as $column) {
        if (!empty($column['Field'])) {
            $tableColumns[] = $column['Field'];
        }
    }
} catch (Throwable $exception) {
    error_log(
        'Article columns error: ' . $exception->getMessage()
    );

    $errors[] = 'ساختار جدول articles قابل شناسایی نیست.';
}

/*
|--------------------------------------------------------------------------
| بارگذاری مقاله
|--------------------------------------------------------------------------
*/

if (!$errors) {
    try {
        if (in_array('deleted_at', $tableColumns, true)) {
            $articleStatement = db()->prepare(
                'SELECT *
                 FROM articles
                 WHERE id = :id
                   AND deleted_at IS NULL
                 LIMIT 1'
            );
        } else {
            $articleStatement = db()->prepare(
                'SELECT *
                 FROM articles
                 WHERE id = :id
                 LIMIT 1'
            );
        }

        $articleStatement->execute([
            'id' => $articleId,
        ]);

        $article = $articleStatement->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$article) {
            header('Location: articles.php');
            exit;
        }
    } catch (Throwable $exception) {
        error_log(
            'Article load error: ' . $exception->getMessage()
        );

        $errors[] = 'اطلاعات مقاله بارگذاری نشد.';
    }
}

/*
|--------------------------------------------------------------------------
| بارگذاری دسته‌بندی‌ها
|--------------------------------------------------------------------------
*/

try {
    $categoryColumnsStatement = db()->query(
        'SHOW COLUMNS FROM categories'
    );

    $categoryColumns = $categoryColumnsStatement->fetchAll(
        PDO::FETCH_ASSOC
    );

    $categoryColumnNames = [];

    foreach ($categoryColumns as $column) {
        if (!empty($column['Field'])) {
            $categoryColumnNames[] = $column['Field'];
        }
    }

    $categoryWhere = '';

    if (in_array('deleted_at', $categoryColumnNames, true)) {
        $categoryWhere = 'WHERE deleted_at IS NULL';
    }

    $categoryStatement = db()->query(
        "SELECT id, name
         FROM categories
         {$categoryWhere}
         ORDER BY name ASC"
    );

    $categories = $categoryStatement->fetchAll(
        PDO::FETCH_ASSOC
    );
} catch (Throwable $exception) {
    error_log(
        'Categories load error: ' . $exception->getMessage()
    );
}

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = $_SESSION['csrf_token'];

/*
|--------------------------------------------------------------------------
| مقدارهای اولیه مقاله
|--------------------------------------------------------------------------
*/

$storedBlocks = [];

if (is_array($article)) {
    $blocksSource = '';

    if (
        array_key_exists('content_blocks', $article)
        && $article['content_blocks'] !== null
    ) {
        $blocksSource = (string) $article['content_blocks'];
    }

    if ($blocksSource !== '') {
        $decodedBlocks = json_decode(
            $blocksSource,
            true
        );

        if (is_array($decodedBlocks)) {
            $storedBlocks = ae_clean_blocks(
                $decodedBlocks
            );
        }
    }
}

$old = [
    'title' => (string) ($article['title'] ?? ''),
    'slug' => (string) ($article['slug'] ?? ''),
    'excerpt' => (string) ($article['excerpt'] ?? ''),
    'status' => (string) ($article['status'] ?? 'draft'),
    'category_id' => (int) ($article['category_id'] ?? 0),
    'seo_title' => (string) ($article['seo_title'] ?? ''),
    'meta_description' => (string) (
        $article['meta_description'] ?? ''
    ),
    'focus_keyword' => (string) (
        $article['focus_keyword'] ?? ''
    ),
    'cover_image' => (string) (
        $article['cover_image'] ?? ''
    ),
];

/*
|--------------------------------------------------------------------------
| ویرایش مقاله
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string) (
        $_POST['csrf_token'] ?? ''
    );

    if (
        $postedToken === ''
        || !hash_equals($csrfToken, $postedToken)
    ) {
        $errors[] = 'درخواست نامعتبر است. صفحه را دوباره بارگذاری کنید.';
    }

    $title = trim(
        (string) ($_POST['title'] ?? '')
    );

    $slug = ae_slug(
        (string) ($_POST['slug'] ?? '')
    );

    $excerpt = trim(
        (string) ($_POST['excerpt'] ?? '')
    );

    $status = (string) (
        $_POST['status'] ?? 'draft'
    );

    $categoryId = (int) (
        $_POST['category_id'] ?? 0
    );

    $seoTitle = trim(
        (string) ($_POST['seo_title'] ?? '')
    );

    $metaDescription = trim(
        (string) ($_POST['meta_description'] ?? '')
    );

    $focusKeyword = trim(
        (string) ($_POST['focus_keyword'] ?? '')
    );

    $coverImage = ae_url(
        (string) ($_POST['cover_image'] ?? '')
    );

    $contentBlocksText = (string) (
        $_POST['content_blocks'] ?? '[]'
    );

    $decodedBlocks = json_decode(
        $contentBlocksText,
        true
    );

    if (!is_array($decodedBlocks)) {
        $errors[] = 'ساختار محتوای مقاله معتبر نیست.';
        $decodedBlocks = [];
    }

    $contentBlocks = ae_clean_blocks(
        $decodedBlocks
    );

    if ($title === '') {
        $errors[] = 'عنوان مقاله را وارد کنید.';
    } elseif (mb_strlen($title, 'UTF-8') < 5) {
        $errors[] = 'عنوان مقاله باید حداقل ۵ کاراکتر باشد.';
    }

    if ($slug === '') {
        $slug = ae_slug($title);
    }

    if ($slug === '') {
        $errors[] = 'نامک مقاله معتبر نیست.';
    }

    if (
        $excerpt !== ''
        && mb_strlen($excerpt, 'UTF-8') > 500
    ) {
        $errors[] = 'خلاصه مقاله نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد.';
    }

    if (!in_array(
        $status,
        ['draft', 'pending', 'published'],
        true
    )) {
        $status = 'draft';
    }

    if ($seoTitle === '') {
        $seoTitle = $title;
    }

    if (
        $metaDescription !== ''
        && mb_strlen($metaDescription, 'UTF-8') > 320
    ) {
        $errors[] = 'توضیحات متا نمی‌تواند بیشتر از ۳۲۰ کاراکتر باشد.';
    }

    /*
    |--------------------------------------------------------------------------
    | بررسی نامک تکراری
    |--------------------------------------------------------------------------
    */

    if (
        !$errors
        && in_array('slug', $tableColumns, true)
    ) {
        try {
            $duplicateStatement = db()->prepare(
                'SELECT id
                 FROM articles
                 WHERE slug = :slug
                   AND id <> :id
                 LIMIT 1'
            );

            $duplicateStatement->execute([
                'slug' => $slug,
                'id' => $articleId,
            ]);

            if ($duplicateStatement->fetch(
                PDO::FETCH_ASSOC
            )) {
                $errors[] = 'این نامک قبلاً برای مقاله دیگری استفاده شده است.';
            }
        } catch (Throwable $exception) {
            error_log(
                'Duplicate slug error: ' . $exception->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ذخیره تغییرات
    |--------------------------------------------------------------------------
    */

    if (!$errors) {
        try {
            $updateData = [];

            $updateData['title'] = $title;

            if (in_array('slug', $tableColumns, true)) {
                $updateData['slug'] = $slug;
            }

            if (in_array('excerpt', $tableColumns, true)) {
                $updateData['excerpt'] = $excerpt !== ''
                    ? $excerpt
                    : null;
            }

            if (in_array('status', $tableColumns, true)) {
                $updateData['status'] = $status;
            }

            if (in_array('category_id', $tableColumns, true)) {
                $updateData['category_id'] = $categoryId > 0
                    ? $categoryId
                    : null;
            }

            if (in_array('seo_title', $tableColumns, true)) {
                $updateData['seo_title'] = $seoTitle !== ''
                    ? $seoTitle
                    : null;
            }

            if (in_array(
                'meta_description',
                $tableColumns,
                true
            )) {
                $updateData['meta_description'] = $metaDescription !== ''
                    ? $metaDescription
                    : null;
            }

            if (in_array(
                'focus_keyword',
                $tableColumns,
                true
            )) {
                $updateData['focus_keyword'] = $focusKeyword !== ''
                    ? $focusKeyword
                    : null;
            }

            if (in_array(
                'cover_image',
                $tableColumns,
                true
            )) {
                $updateData['cover_image'] = $coverImage !== ''
                    ? $coverImage
                    : null;
            }

            $blocksJson = json_encode(
                $contentBlocks,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

            if (in_array(
                'content_blocks',
                $tableColumns,
                true
            )) {
                $updateData['content_blocks'] = $blocksJson;
            }

            if (in_array('content', $tableColumns, true)) {
                $oldContent = (string) (
                    $article['content'] ?? ''
                );

                $updateData['content'] = $oldContent;
            }

            if (in_array('updated_at', $tableColumns, true)) {
                $updateData['updated_at'] = date(
                    'Y-m-d H:i:s'
                );
            }

            if (
                $status === 'published'
                && in_array('published_at', $tableColumns, true)
            ) {
                if (!empty($article['published_at'])) {
                    $updateData['published_at'] = $article['published_at'];
                } else {
                    $updateData['published_at'] = date(
                        'Y-m-d H:i:s'
                    );
                }
            }

            if (
                $status !== 'published'
                && in_array('published_at', $tableColumns, true)
            ) {
                $updateData['published_at'] = null;
            }

            $setParts = [];
            $parameters = [
                'article_id' => $articleId,
            ];

            foreach ($updateData as $column => $value) {
                $parameterName = 'value_' . $column;

                $setParts[] = "`{$column}` = :{$parameterName}";
                $parameters[$parameterName] = $value;
            }

            if (!$setParts) {
                throw new RuntimeException(
                    'No columns available for update.'
                );
            }

            $sql = sprintf(
                'UPDATE articles
                 SET %s
                 WHERE id = :article_id
                 LIMIT 1',
                implode(",\n", $setParts)
            );

            $updateStatement = db()->prepare($sql);
            $updateStatement->execute($parameters);

            header(
                'Location: articles.php?updated=1'
            );
            exit;
        } catch (Throwable $exception) {
            error_log(
                'Article update error: '
                . $exception->getMessage()
            );

            $errors[] = 'ذخیره تغییرات انجام نشد. جزئیات خطا در لاگ ثبت شد.';
        }
    }

    $old = [
        'title' => $title,
        'slug' => $slug,
        'excerpt' => $excerpt,
        'status' => $status,
        'category_id' => $categoryId,
        'seo_title' => $seoTitle,
        'meta_description' => $metaDescription,
        'focus_keyword' => $focusKeyword,
        'cover_image' => $coverImage,
    ];

    $storedBlocks = $contentBlocks;
}

$blocksJsonForForm = json_encode(
    $storedBlocks,
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_PRETTY_PRINT
);

if ($blocksJsonForForm === false) {
    $blocksJsonForForm = '[]';
}

?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>ویرایش مقاله | پنل مدیریت</title>

    <link
        rel="stylesheet"
        href="assets/admin.css"
    >

    <style>
        body {
            background: #f7f8fb;
        }

        .edit-wrapper {
            max-width: 1250px;
            margin: 0 auto;
        }

        .edit-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
            gap: 18px;
        }

        .card {
            margin-bottom: 18px;
            padding: 20px;
            border: 1px solid #e4e7ee;
            border-radius: 14px;
            background: #fff;
        }

        .card h2 {
            margin: 0 0 18px;
            color: #303746;
            font-size: 15px;
        }

        .field {
            margin-bottom: 15px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            color: #687386;
            font-size: 12px;
            font-weight: bold;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #e4e7ee;
            border-radius: 8px;
            outline: none;
            color: #303746;
            font-family: inherit;
            font-size: 13px;
            box-sizing: border-box;
        }

        .field textarea {
            min-height: 120px;
            line-height: 2;
            resize: vertical;
        }

        .field textarea.json-editor {
            min-height: 420px;
            direction: ltr;
            text-align: left;
            font-family: Consolas, monospace;
            font-size: 12px;
            line-height: 1.7;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: #6654d9;
            box-shadow: 0 0 0 3px rgba(102, 84, 217, .1);
        }

        .hint {
            margin-top: 7px;
            color: #8b94a5;
            font-size: 11px;
            line-height: 1.8;
        }

        .error-box {
            margin-bottom: 18px;
            padding: 13px 17px;
            border: 1px solid #ffd0d0;
            border-radius: 9px;
            color: #a43e3e;
            background: #fff1f1;
            font-size: 13px;
            line-height: 2;
        }

        .error-box ul {
            margin: 0;
            padding-right: 20px;
        }

        .preview {
            overflow: hidden;
            margin-top: 15px;
            padding: 15px;
            border: 1px solid #e4e7ee;
            border-radius: 10px;
            background: #fafbfc;
        }

        .preview-title {
            margin-bottom: 12px;
            color: #303746;
            font-size: 13px;
            font-weight: bold;
        }

        .preview-content {
            white-space: pre-wrap;
            color: #687386;
            font-size: 12px;
            line-height: 2;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 43px;
            padding: 0 20px;
            border: 0;
            border-radius: 8px;
            text-decoration: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-primary {
            color: #fff;
            background: #6654d9;
        }

        .btn-secondary {
            color: #687386;
            background: #edf0f4;
        }

        .article-meta {
            margin-bottom: 18px;
            color: #8b94a5;
            font-size: 11px;
        }

        .article-meta strong {
            color: #6654d9;
        }

        @media (max-width: 900px) {
            .edit-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="admin-shell">

    <?php
    $sidebarFile = __DIR__ . '/includes/sidebar.php';

    if (is_file($sidebarFile)) {
        require $sidebarFile;
    }
    ?>

    <div class="workspace">

        <header class="topbar">
            <div class="breadcrumb">
                صفحات
                <span> / </span>
                <a href="articles.php">مقالات</a>
                <span> / </span>
                <strong>ویرایش مقاله</strong>
            </div>

            <div class="profile">
                <div class="avatar">
                    <?= ae_escape(
                        mb_substr(
                            (string) ($admin['name'] ?? 'م'),
                            0,
                            1,
                            'UTF-8'
                        )
                    ) ?>
                </div>

                <div>
                    <strong>
                        <?= ae_escape(
                            $admin['name'] ?? 'مدیر'
                        ) ?>
                    </strong>

                    <small>مدیر سیستم</small>
                </div>
            </div>
        </header>

        <main class="page-content">

            <div class="edit-wrapper">

                <div class="page-heading">
                    <div>
                        <h1>ویرایش مقاله</h1>
                        <p>
                            اطلاعات مقاله را ویرایش و ذخیره کنید.
                        </p>
                    </div>

                    <a
                        href="articles.php"
                        class="btn btn-secondary"
                    >
                        بازگشت به مقالات
                    </a>
                </div>

                <?php if ($errors): ?>
                    <div class="error-box">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li>
                                    <?= ae_escape($error) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($article): ?>

                    <div class="article-meta">
                        شناسه مقاله:
                        <strong><?= $articleId ?></strong>

                        <?php if (!empty($article['created_at'])): ?>
                            <span>
                                |
                                تاریخ ایجاد:
                                <?= ae_escape(
                                    $article['created_at']
                                ) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <form
                        method="post"
                        id="article-edit-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= ae_escape($csrfToken) ?>"
                        >

                        <input
                            type="hidden"
                            name="article_id"
                            value="<?= $articleId ?>"
                        >

                        <div class="edit-grid">

                            <section>

                                <div class="card">

                                    <h2>اطلاعات اصلی مقاله</h2>

                                    <div class="field">
                                        <label for="title">
                                            عنوان مقاله
                                        </label>

                                        <input
                                            id="title"
                                            name="title"
                                            type="text"
                                            value="<?= ae_old(
                                                'title',
                                                $old['title']
                                            ) ?>"
                                            required
                                        >
                                    </div>

                                    <div class="field">
                                        <label for="slug">
                                            نامک مقاله
                                        </label>

                                        <input
                                            id="slug"
                                            name="slug"
                                            type="text"
                                            dir="ltr"
                                            value="<?= ae_old(
                                                'slug',
                                                $old['slug']
                                            ) ?>"
                                        >

                                        <div class="hint">
                                            نامک فقط برای آدرس مقاله استفاده می‌شود.
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label for="excerpt">
                                            خلاصه مقاله
                                        </label>

                                        <textarea
                                            id="excerpt"
                                            name="excerpt"
                                            maxlength="500"
                                        ><?= ae_old(
                                            'excerpt',
                                            $old['excerpt']
                                        ) ?></textarea>
                                    </div>

                                </div>

                                <div class="card">

                                    <h2>محتوای مقاله</h2>

                                    <div class="field">
                                        <label for="content_blocks">
                                            محتوای بلوکی مقاله
                                        </label>

                                        <textarea
                                            id="content_blocks"
                                            name="content_blocks"
                                            class="json-editor"
                                            spellcheck="false"
                                        ><?= ae_escape(
                                            $blocksJsonForForm
                                        ) ?></textarea>

                                        <div class="hint">
                                            این بخش اطلاعات المان‌های مقاله است.
                                            اگر محتوای قبلی وجود داشته باشد،
                                            همین‌جا نمایش داده می‌شود.
                                            هنگام ذخیره، ساختار JSON را خراب نکنید.
                                        </div>
                                    </div>

                                    <?php if (!empty($storedBlocks)): ?>
                                        <div class="preview">
                                            <div class="preview-title">
                                                خلاصه محتوای ذخیره‌شده
                                            </div>

                                            <div class="preview-content">
                                                <?php foreach (
                                                    $storedBlocks
                                                    as $index => $block
                                                ): ?>
                                                    بخش
                                                    <?= $index + 1 ?>
                                                    :
                                                    <?= ae_escape(
                                                        $block['type'] ?? ''
                                                    ) ?>

                                                    <?php if (
                                                        ($block['type'] ?? '')
                                                        === 'heading'
                                                    ): ?>
                                                        -
                                                        <?= ae_escape(
                                                            $block['data']['text']
                                                            ?? ''
                                                        ) ?>
                                                    <?php endif; ?>

                                                    <?php if (
                                                        ($block['type'] ?? '')
                                                        === 'paragraph'
                                                    ): ?>
                                                        -
                                                        <?= ae_escape(
                                                            strip_tags(
                                                                $block['data']['html']
                                                                ?? ''
                                                            )
                                                        ) ?>
                                                    <?php endif; ?>

                                                    <br>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <div class="actions">
                                    <a
                                        href="articles.php"
                                        class="btn btn-secondary"
                                    >
                                        انصراف
                                    </a>

                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        ذخیره تغییرات
                                    </button>
                                </div>

                            </section>

                            <aside>

                                <div class="card">

                                    <h2>تنظیمات انتشار</h2>

                                    <div class="field">
                                        <label for="status">
                                            وضعیت مقاله
                                        </label>

                                        <select
                                            id="status"
                                            name="status"
                                        >
                                            <option
                                                value="draft"
                                                <?= $old['status'] === 'draft'
                                                    ? 'selected'
                                                    : '' ?>
                                            >
                                                پیش‌نویس
                                            </option>

                                            <option
                                                value="pending"
                                                <?= $old['status'] === 'pending'
                                                    ? 'selected'
                                                    : '' ?>
                                            >
                                                در انتظار بررسی
                                            </option>

                                            <option
                                                value="published"
                                                <?= $old['status'] === 'published'
                                                    ? 'selected'
                                                    : '' ?>
                                            >
                                                منتشرشده
                                            </option>
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label for="category_id">
                                            دسته‌بندی
                                        </label>

                                        <select
                                            id="category_id"
                                            name="category_id"
                                        >
                                            <option value="0">
                                                بدون دسته‌بندی
                                            </option>

                                            <?php foreach (
                                                $categories
                                                as $category
                                            ): ?>
                                                <option
                                                    value="<?= (int) $category['id'] ?>"
                                                    <?= (int) $old['category_id']
                                                        === (int) $category['id']
                                                        ? 'selected'
                                                        : '' ?>
                                                >
                                                    <?= ae_escape(
                                                        $category['name']
                                                    ) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label for="cover_image">
                                            تصویر شاخص
                                        </label>

                                        <input
                                            id="cover_image"
                                            name="cover_image"
                                            type="url"
                                            dir="ltr"
                                            value="<?= ae_old(
                                                'cover_image',
                                                $old['cover_image']
                                            ) ?>"
                                        >
                                    </div>

                                </div>

                                <div class="card">

                                    <h2>تنظیمات سئو</h2>

                                    <div class="field">
                                        <label for="seo_title">
                                            عنوان سئو
                                        </label>

                                        <input
                                            id="seo_title"
                                            name="seo_title"
                                            type="text"
                                            value="<?= ae_old(
                                                'seo_title',
                                                $old['seo_title']
                                            ) ?>"
                                        >
                                    </div>

                                    <div class="field">
                                        <label for="meta_description">
                                            توضیحات متا
                                        </label>

                                        <textarea
                                            id="meta_description"
                                            name="meta_description"
                                            maxlength="320"
                                        ><?= ae_old(
                                            'meta_description',
                                            $old['meta_description']
                                        ) ?></textarea>
                                    </div>

                                    <div class="field">
                                        <label for="focus_keyword">
                                            کلمه کلیدی اصلی
                                        </label>

                                        <input
                                            id="focus_keyword"
                                            name="focus_keyword"
                                            type="text"
                                            value="<?= ae_old(
                                                'focus_keyword',
                                                $old['focus_keyword']
                                            ) ?>"
                                        >
                                    </div>

                                </div>

                            </aside>

                        </div>

                    </form>

                <?php endif; ?>

            </div>

        </main>

    </div>

</div>

<script>
(function () {
    const title = document.getElementById('title');
    const slug = document.getElementById('slug');

    if (!title || !slug) {
        return;
    }

    title.addEventListener('blur', function () {
        if (slug.value.trim() !== '') {
            return;
        }

        let value = title.value.trim();

        value = value
            .replace(/[يى]/g, 'ی')
            .replace(/ك/g, 'ک')
            .replace(/\s+/g, '-')
            .replace(/[^\p{L}\p{N}\-]/gu, '')
            .replace(/\-+/g, '-')
            .replace(/^\-|\-$/g, '');

        slug.value = value;
    });
})();
</script>

</body>
</html>