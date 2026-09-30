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

function ac_escape(?string $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function ac_slug(string $value): string
{
    $value = trim($value);

    $value = str_replace(
        ['ي', 'ى', 'ك', 'ة', 'ؤ', 'إ', 'أ', 'ۀ', '‌'],
        ['ی', 'ی', 'ک', 'ه', 'و', 'ا', 'ا', 'ه', '-'],
        $value
    );

    $value = preg_replace('/[\s_]+/u', '-', $value) ?? $value;
    $value = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $value) ?? $value;
    $value = preg_replace('/-+/u', '-', $value) ?? $value;

    return trim($value, '-');
}

function ac_valid_url(string $url): string
{
    $url = trim($url);

    if ($url === '') {
        return '';
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    if (!in_array($scheme, ['http', 'https'], true)) {
        return '';
    }

    return $url;
}

function ac_clean_text(string $value, int $maxLength = 0): string
{
    $value = trim(strip_tags($value));

    if ($maxLength > 0) {
        $value = mb_substr($value, 0, $maxLength, 'UTF-8');
    }

    return $value;
}

function ac_clean_html(string $html): string
{
    $allowedTags = '<p><br><strong><b><em><i><u><s><ul><ol><li><a><blockquote><code><pre>';

    $html = strip_tags($html, $allowedTags);

    $html = preg_replace(
        '/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu',
        '',
        $html
    ) ?? $html;

    $html = preg_replace(
        '/javascript\s*:/iu',
        '',
        $html
    ) ?? $html;

    $html = preg_replace(
        '/(href|src)\s*=\s*(["\'])\s*javascript:.*?\2/iu',
        '$1="#"',
        $html
    ) ?? $html;

    return trim($html);
}

function ac_clean_blocks(array $blocks): array
{
    $allowedTypes = [
        'paragraph',
        'heading',
        'image',
        'quote',
        'video',
        'button',
        'alert',
        'code',
        'faq',
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

        $data = is_array($block['data'] ?? null)
            ? $block['data']
            : [];

        switch ($type) {
            case 'paragraph':
                $cleanData = [
                    'html' => ac_clean_html(
                        (string) ($data['html'] ?? '')
                    ),
                ];
                break;

            case 'heading':
                $level = (string) ($data['level'] ?? 'h2');

                if (!in_array($level, ['h2', 'h3', 'h4'], true)) {
                    $level = 'h2';
                }

                $cleanData = [
                    'level' => $level,
                    'text' => ac_clean_text(
                        (string) ($data['text'] ?? ''),
                        500
                    ),
                ];
                break;

            case 'image':
                $cleanData = [
                    'url' => ac_valid_url(
                        (string) ($data['url'] ?? '')
                    ),
                    'alt' => ac_clean_text(
                        (string) ($data['alt'] ?? ''),
                        255
                    ),
                    'caption' => ac_clean_text(
                        (string) ($data['caption'] ?? ''),
                        500
                    ),
                ];
                break;

            case 'quote':
                $cleanData = [
                    'text' => ac_clean_text(
                        (string) ($data['text'] ?? ''),
                        2000
                    ),
                    'author' => ac_clean_text(
                        (string) ($data['author'] ?? ''),
                        255
                    ),
                ];
                break;

            case 'video':
                $cleanData = [
                    'url' => ac_valid_url(
                        (string) ($data['url'] ?? '')
                    ),
                    'title' => ac_clean_text(
                        (string) ($data['title'] ?? ''),
                        255
                    ),
                ];
                break;

            case 'button':
                $style = (string) ($data['style'] ?? 'primary');

                if (!in_array($style, ['primary', 'secondary'], true)) {
                    $style = 'primary';
                }

                $cleanData = [
                    'text' => ac_clean_text(
                        (string) ($data['text'] ?? 'مشاهده بیشتر'),
                        150
                    ),
                    'url' => ac_valid_url(
                        (string) ($data['url'] ?? '')
                    ),
                    'style' => $style,
                ];
                break;

            case 'alert':
                $alertType = (string) ($data['alert_type'] ?? 'info');

                if (!in_array(
                    $alertType,
                    ['info', 'success', 'warning', 'danger'],
                    true
                )) {
                    $alertType = 'info';
                }

                $cleanData = [
                    'title' => ac_clean_text(
                        (string) ($data['title'] ?? 'نکته مهم'),
                        255
                    ),
                    'text' => ac_clean_text(
                        (string) ($data['text'] ?? ''),
                        2000
                    ),
                    'alert_type' => $alertType,
                ];
                break;

            case 'code':
                $cleanData = [
                    'language' => ac_clean_text(
                        (string) ($data['language'] ?? 'text'),
                        50
                    ),
                    'code' => (string) ($data['code'] ?? ''),
                ];
                break;

            case 'faq':
                $items = [];

                foreach (($data['items'] ?? []) as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $question = ac_clean_text(
                        (string) ($item['question'] ?? ''),
                        500
                    );

                    $answer = ac_clean_html(
                        (string) ($item['answer'] ?? '')
                    );

                    if ($question !== '' && $answer !== '') {
                        $items[] = [
                            'question' => $question,
                            'answer' => $answer,
                        ];
                    }
                }

                $cleanData = [
                    'items' => array_slice($items, 0, 20),
                ];
                break;

            case 'divider':
                $style = (string) ($data['style'] ?? 'solid');

                if (!in_array($style, ['solid', 'dashed', 'dotted'], true)) {
                    $style = 'solid';
                }

                $cleanData = [
                    'style' => $style,
                ];
                break;

            case 'spacer':
                $cleanData = [
                    'height' => max(
                        10,
                        min(300, (int) ($data['height'] ?? 40))
                    ),
                ];
                break;

            default:
                $cleanData = [];
        }

        $result[] = [
            'id' => bin2hex(random_bytes(8)),
            'type' => $type,
            'data' => $cleanData,
        ];
    }

    return $result;
}

function ac_blocks_to_html(array $blocks): string
{
    $html = [];

    foreach ($blocks as $block) {
        $type = (string) ($block['type'] ?? '');
        $data = is_array($block['data'] ?? null)
            ? $block['data']
            : [];

        switch ($type) {
            case 'paragraph':
                $content = trim((string) ($data['html'] ?? ''));

                if ($content !== '') {
                    $html[] = '<div class="article-paragraph">'
                        . $content
                        . '</div>';
                }
                break;

            case 'heading':
                $level = in_array(
                    ($data['level'] ?? ''),
                    ['h2', 'h3', 'h4'],
                    true
                )
                    ? $data['level']
                    : 'h2';

                $text = ac_escape((string) ($data['text'] ?? ''));

                if ($text !== '') {
                    $html[] = sprintf(
                        '<%1$s>%2$s</%1$s>',
                        $level,
                        $text
                    );
                }
                break;

            case 'image':
                $url = ac_escape((string) ($data['url'] ?? ''));
                $alt = ac_escape((string) ($data['alt'] ?? ''));
                $caption = ac_escape((string) ($data['caption'] ?? ''));

                if ($url !== '') {
                    $imageHtml = '<figure class="article-image">'
                        . '<img src="' . $url . '" alt="' . $alt . '" loading="lazy">';

                    if ($caption !== '') {
                        $imageHtml .= '<figcaption>'
                            . $caption
                            . '</figcaption>';
                    }

                    $imageHtml .= '</figure>';

                    $html[] = $imageHtml;
                }
                break;

            case 'quote':
                $text = ac_escape((string) ($data['text'] ?? ''));
                $author = ac_escape((string) ($data['author'] ?? ''));

                if ($text !== '') {
                    $quoteHtml = '<blockquote>'
                        . '<p>' . nl2br($text) . '</p>';

                    if ($author !== '') {
                        $quoteHtml .= '<cite>' . $author . '</cite>';
                    }

                    $quoteHtml .= '</blockquote>';

                    $html[] = $quoteHtml;
                }
                break;

            case 'video':
                $url = ac_escape((string) ($data['url'] ?? ''));
                $title = ac_escape((string) ($data['title'] ?? 'ویدئو'));

                if ($url !== '') {
                    $html[] = '<p class="article-video">'
                        . '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">'
                        . $title
                        . '</a></p>';
                }
                break;

            case 'button':
                $url = ac_escape((string) ($data['url'] ?? ''));
                $text = ac_escape((string) ($data['text'] ?? 'مشاهده بیشتر'));
                $style = ($data['style'] ?? 'primary') === 'secondary'
                    ? 'secondary'
                    : 'primary';

                if ($url !== '') {
                    $html[] = '<p class="article-button article-button-'
                        . $style
                        . '">'
                        . '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">'
                        . $text
                        . '</a></p>';
                }
                break;

            case 'alert':
                $title = ac_escape((string) ($data['title'] ?? ''));
                $text = ac_escape((string) ($data['text'] ?? ''));
                $alertType = ac_escape((string) ($data['alert_type'] ?? 'info'));

                if ($title !== '' || $text !== '') {
                    $html[] = '<div class="article-alert alert-'
                        . $alertType
                        . '">';

                    if ($title !== '') {
                        $html[] = '<strong>' . $title . '</strong>';
                    }

                    if ($text !== '') {
                        $html[] = '<p>' . nl2br($text) . '</p>';
                    }

                    $html[] = '</div>';
                }
                break;

            case 'code':
                $language = ac_escape((string) ($data['language'] ?? 'text'));
                $code = ac_escape((string) ($data['code'] ?? ''));

                if ($code !== '') {
                    $html[] = '<pre data-language="' . $language . '"><code>'
                        . $code
                        . '</code></pre>';
                }
                break;

            case 'faq':
                $items = $data['items'] ?? [];

                if (is_array($items) && $items !== []) {
                    $faqHtml = '<section class="article-faq">';

                    foreach ($items as $item) {
                        if (!is_array($item)) {
                            continue;
                        }

                        $question = ac_escape(
                            (string) ($item['question'] ?? '')
                        );

                        $answer = ac_clean_html(
                            (string) ($item['answer'] ?? '')
                        );

                        if ($question !== '' && $answer !== '') {
                            $faqHtml .= '<details>'
                                . '<summary>' . $question . '</summary>'
                                . '<div>' . $answer . '</div>'
                                . '</details>';
                        }
                    }

                    $faqHtml .= '</section>';
                    $html[] = $faqHtml;
                }
                break;

            case 'divider':
                $style = in_array(
                    ($data['style'] ?? ''),
                    ['solid', 'dashed', 'dotted'],
                    true
                )
                    ? $data['style']
                    : 'solid';

                $html[] = '<hr class="article-divider divider-'
                    . ac_escape((string) $style)
                    . '">';
                break;

            case 'spacer':
                $height = max(
                    10,
                    min(300, (int) ($data['height'] ?? 40))
                );

                $html[] = '<div style="height:'
                    . $height
                    . 'px"></div>';
                break;
        }
    }

    return trim(implode("\n", $html));
}

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['article_create_csrf'])) {
    $_SESSION['article_create_csrf'] = bin2hex(random_bytes(32));
}

$csrfToken = (string) $_SESSION['article_create_csrf'];

$errors = [];

$old = [
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'status' => 'draft',
    'published_at' => '',
    'cover_image' => '',
    'cover_alt' => '',
    'seo_title' => '',
    'meta_description' => '',
    'canonical_url' => '',
    'robots_index' => '1',
    'robots_follow' => '1',
    'focus_keyword' => '',
    'content_blocks' => '[]',
];

/*
|--------------------------------------------------------------------------
| ثبت مقاله
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string) ($_POST['csrf_token'] ?? '');

    if (
        $postedToken === '' ||
        !hash_equals($csrfToken, $postedToken)
    ) {
        $errors[] = 'درخواست نامعتبر است. صفحه را دوباره بارگذاری کنید.';
    }

    foreach ($old as $key => $default) {
        if (array_key_exists($key, $_POST)) {
            $old[$key] = is_string($_POST[$key])
                ? $_POST[$key]
                : $default;
        }
    }

    $title = ac_clean_text($old['title'], 255);
    $slug = ac_slug($old['slug']);
    $excerpt = ac_clean_text($old['excerpt']);
    $status = $old['status'];

    $publishedAtInput = trim($old['published_at']);

    $coverImage = ac_valid_url($old['cover_image']);
    $coverAlt = ac_clean_text($old['cover_alt'], 255);

    $seoTitle = ac_clean_text($old['seo_title'], 255);
    $metaDescription = ac_clean_text($old['meta_description'], 320);
    $canonicalUrl = ac_valid_url($old['canonical_url']);
    $focusKeyword = ac_clean_text($old['focus_keyword'], 150);

    $robotsIndex = $old['robots_index'] === '0' ? 0 : 1;
    $robotsFollow = $old['robots_follow'] === '0' ? 0 : 1;

    $decodedBlocks = json_decode(
        $old['content_blocks'],
        true
    );

    if (!is_array($decodedBlocks)) {
        $decodedBlocks = [];
    }

    $contentBlocks = ac_clean_blocks($decodedBlocks);
    $body = ac_blocks_to_html($contentBlocks);

    if ($title === '') {
        $errors[] = 'عنوان مقاله را وارد کنید.';
    } elseif (mb_strlen($title, 'UTF-8') < 5) {
        $errors[] = 'عنوان مقاله باید حداقل ۵ کاراکتر باشد.';
    }

    if ($slug === '') {
        $slug = ac_slug($title);
        $old['slug'] = $slug;
    }

    if ($slug === '') {
        $errors[] = 'نامک مقاله معتبر نیست.';
    } elseif (mb_strlen($slug, 'UTF-8') > 191) {
        $errors[] = 'نامک مقاله نمی‌تواند بیشتر از ۱۹۱ کاراکتر باشد.';
    }

    if (mb_strlen($excerpt, 'UTF-8') > 500) {
        $errors[] = 'خلاصه مقاله نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد.';
    }

    if (!in_array(
        $status,
        ['draft', 'scheduled', 'published', 'archived'],
        true
    )) {
        $status = 'draft';
        $old['status'] = 'draft';
    }

    if ($seoTitle === '') {
        $seoTitle = $title;
        $old['seo_title'] = $seoTitle;
    }

    if ($status === 'scheduled') {
        if ($publishedAtInput === '') {
            $errors[] = 'برای مقاله زمان انتشار را مشخص کنید.';
        } else {
            $date = DateTime::createFromFormat(
                'Y-m-d\TH:i',
                $publishedAtInput
            );

            if (!$date) {
                $errors[] = 'تاریخ انتشار معتبر نیست.';
            }
        }
    }

    if ($coverImage !== '' && $coverAlt === '') {
        $errors[] = 'برای تصویر کاور متن جایگزین وارد کنید.';
    }

    if ($body === '') {
        $errors[] = 'متن مقاله نمی‌تواند خالی باشد.';
    }

    /*
    |--------------------------------------------------------------------------
    | تعیین تاریخ انتشار
    |--------------------------------------------------------------------------
    */

    $publishedAt = null;

    if ($status === 'published') {
        $publishedAt = date('Y-m-d H:i:s');
    } elseif ($status === 'scheduled' && $publishedAtInput !== '') {
        $date = DateTime::createFromFormat(
            'Y-m-d\TH:i',
            $publishedAtInput
        );

        if ($date instanceof DateTime) {
            $publishedAt = $date->format('Y-m-d H:i:s');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | بررسی تکراری نبودن نامک
    |--------------------------------------------------------------------------
    */

    if (!$errors) {
        try {
            $duplicateStmt = db()->prepare(
                'SELECT id
                 FROM articles
                 WHERE slug = :slug
                 LIMIT 1'
            );

            $duplicateStmt->execute([
                'slug' => $slug,
            ]);

            if ($duplicateStmt->fetch(PDO::FETCH_ASSOC)) {
                $errors[] = 'این نامک قبلاً استفاده شده است.';
            }
        } catch (Throwable $exception) {
            error_log('Slug check error: ' . $exception->getMessage());
            $errors[] = 'بررسی نامک انجام نشد.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ذخیره مقاله
    |--------------------------------------------------------------------------
    */

    if (!$errors) {
        try {
            $authorId = null;

            if (isset($admin['id']) && (int) $admin['id'] > 0) {
                $authorId = (int) $admin['id'];
            }

            $insertStmt = db()->prepare(
                'INSERT INTO articles (
                    author_id,
                    title,
                    slug,
                    excerpt,
                    body,
                    status,
                    published_at,
                    cover_image,
                    cover_alt,
                    seo_title,
                    meta_description,
                    canonical_url,
                    robots_index,
                    robots_follow,
                    created_at,
                    updated_at,
                    focus_keyword
                ) VALUES (
                    :author_id,
                    :title,
                    :slug,
                    :excerpt,
                    :body,
                    :status,
                    :published_at,
                    :cover_image,
                    :cover_alt,
                    :seo_title,
                    :meta_description,
                    :canonical_url,
                    :robots_index,
                    :robots_follow,
                    NOW(),
                    NOW(),
                    :focus_keyword
                )'
            );

            $insertStmt->execute([
                'author_id' => $authorId,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt !== '' ? $excerpt : null,
                'body' => $body,
                'status' => $status,
                'published_at' => $publishedAt,
                'cover_image' => $coverImage !== '' ? $coverImage : null,
                'cover_alt' => $coverAlt !== '' ? $coverAlt : null,
                'seo_title' => $seoTitle !== '' ? $seoTitle : null,
                'meta_description' => $metaDescription !== ''
                    ? $metaDescription
                    : null,
                'canonical_url' => $canonicalUrl !== ''
                    ? $canonicalUrl
                    : null,
                'robots_index' => $robotsIndex,
                'robots_follow' => $robotsFollow,
                'focus_keyword' => $focusKeyword !== ''
                    ? $focusKeyword
                    : null,
            ]);

            unset($_SESSION['article_create_draft']);

            header('Location: articles.php?created=1');
            exit;
        } catch (Throwable $exception) {
            error_log('Article insert error: ' . $exception->getMessage());
            $errors[] = 'ثبت مقاله انجام نشد. اطلاعات اتصال دیتابیس و جدول articles را بررسی کنید.';
        }
    }
}

function ac_old_value(array $old, string $key): string
{
    return ac_escape((string) ($old[$key] ?? ''));
}

$adminName = (string) ($admin['name'] ?? 'مدیر');
$adminInitial = mb_substr($adminName, 0, 1, 'UTF-8');

$initialBlocks = json_decode(
    (string) ($old['content_blocks'] ?? '[]'),
    true
);

if (!is_array($initialBlocks)) {
    $initialBlocks = [];
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>افزودن مقاله | پنل مدیریت</title>

    <link rel="stylesheet" href="assets/admin.css">

    <style>
        :root {
            --primary: #6654d9;
            --primary-light: #f0eeff;
            --text: #303746;
            --muted: #8b94a5;
            --border: #e3e6ed;
            --background: #f7f8fb;
            --danger: #c94b4b;
            --success: #258c58;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--background);
        }

        .page-content {
            max-width: 1450px;
            margin: auto;
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .page-heading h1 {
            margin: 0 0 7px;
            color: var(--text);
            font-size: 23px;
        }

        .page-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        .article-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
            gap: 20px;
        }

        .card {
            margin-bottom: 18px;
            padding: 19px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff;
        }

        .card h2 {
            margin: 0 0 18px;
            color: var(--text);
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
            color: #657083;
            font-size: 11px;
            font-weight: 800;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            min-height: 42px;
            padding: 9px 11px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            color: var(--text);
            background: #fff;
            font: inherit;
            font-size: 12px;
        }

        .field textarea {
            min-height: 100px;
            line-height: 1.9;
            resize: vertical;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 84, 217, .1);
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 10px;
        }

        .error-box {
            margin-bottom: 18px;
            padding: 13px 17px;
            border: 1px solid #ffd1d1;
            border-radius: 9px;
            color: #a23e3e;
            background: #fff1f1;
            font-size: 12px;
            line-height: 2;
        }

        .error-box ul {
            margin: 0;
            padding-right: 20px;
        }

        .builder {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff;
        }

        .builder-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 18px;
            border-bottom: 1px solid var(--border);
        }

        .builder-header h2 {
            margin: 0 0 6px;
            color: var(--text);
            font-size: 15px;
        }

        .builder-header p {
            margin: 0;
            color: var(--muted);
            font-size: 11px;
        }

        .builder-content {
            display: grid;
            grid-template-columns: 205px minmax(0, 1fr);
            min-height: 550px;
        }

        .toolbox {
            padding: 12px;
            border-left: 1px solid var(--border);
            background: #fff;
        }

        .toolbox-title {
            margin: 5px 4px 12px;
            color: var(--text);
            font-size: 12px;
            font-weight: 900;
        }

        .tool {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 8px;
            margin-bottom: 6px;
            padding: 10px;
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--text);
            background: transparent;
            font: inherit;
            font-size: 11px;
            text-align: right;
            cursor: pointer;
        }

        .tool:hover {
            border-color: #ddd8ff;
            background: var(--primary-light);
        }

        .tool-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            height: 27px;
            border-radius: 7px;
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 900;
        }

        .canvas {
            min-width: 0;
            padding: 20px;
            background: var(--background);
        }

        .blocks {
            display: grid;
            gap: 13px;
            max-width: 850px;
            margin: auto;
        }

        .empty {
            padding: 120px 20px;
            color: var(--muted);
            text-align: center;
        }

        .empty strong {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 16px;
        }

        .block {
            overflow: hidden;
            border: 1px solid #dfe3eb;
            border-radius: 10px;
            background: #fff;
        }

        .block-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            border-bottom: 1px solid #edf0f4;
            background: #fbfbfd;
        }

        .block-title {
            color: var(--primary);
            font-size: 11px;
            font-weight: 900;
        }

        .block-actions {
            display: flex;
            gap: 4px;
        }

        .block-actions button {
            width: 27px;
            height: 27px;
            border: 0;
            border-radius: 6px;
            color: #70798a;
            background: #edf0f4;
            cursor: pointer;
        }

        .block-actions button:hover {
            color: #fff;
            background: var(--primary);
        }

        .block-body {
            padding: 15px;
        }

        .block-body input,
        .block-body textarea,
        .block-body select {
            width: 100%;
            margin-bottom: 9px;
            padding: 9px;
            border: 1px solid var(--border);
            border-radius: 7px;
            outline: none;
            font: inherit;
            font-size: 12px;
        }

        .block-body textarea {
            min-height: 120px;
            line-height: 1.9;
            resize: vertical;
        }

        .block-body input:last-child,
        .block-body textarea:last-child,
        .block-body select:last-child {
            margin-bottom: 0;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 8px;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            color: #fff;
            background: var(--primary);
        }

        .button-secondary {
            color: #626d7f;
            background: #e9ecf2;
        }

        .switch-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 13px;
            color: var(--text);
            font-size: 12px;
        }

        .switch-row input {
            width: 17px;
            height: 17px;
            accent-color: var(--primary);
        }

        @media (max-width: 1000px) {
            .article-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .builder-content {
                grid-template-columns: 1fr;
            }

            .toolbox {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 5px;
                border-left: 0;
                border-bottom: 1px solid var(--border);
            }

            .toolbox-title {
                grid-column: 1 / -1;
            }

            .tool {
                margin: 0;
            }

            .builder-header,
            .page-heading,
            .actions {
                align-items: stretch;
                flex-direction: column;
            }

            .button {
                width: 100%;
            }

            .canvas {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

<div class="admin-shell">

    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <div class="workspace">

        <header class="topbar">
            <div class="breadcrumb">
                صفحات
                <span> / </span>
                <a href="articles.php">مقالات</a>
                <span> / </span>
                <strong>افزودن مقاله</strong>
            </div>

            <div class="profile">
                <div class="avatar">
                    <?= ac_escape($adminInitial) ?>
                </div>

                <div>
                    <strong><?= ac_escape($adminName) ?></strong>
                    <small>مدیر سیستم</small>
                </div>
            </div>
        </header>

        <main class="page-content">

            <div class="page-heading">
                <div>
                    <h1>افزودن مقاله جدید</h1>
                    <p>مقاله جدید را ایجاد و در جدول articles ذخیره کنید.</p>
                </div>

                <a class="button button-secondary" href="articles.php">
                    بازگشت به مقالات
                </a>
            </div>

            <?php if ($errors !== []): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= ac_escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" id="article-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= ac_escape($csrfToken) ?>"
                >

                <div class="article-layout">

                    <section>

                        <div class="card">

                            <div class="field">
                                <label for="title">عنوان مقاله</label>

                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    maxlength="255"
                                    value="<?= ac_old_value($old, 'title') ?>"
                                    placeholder="عنوان مقاله را وارد کنید"
                                    required
                                >
                            </div>

                            <div class="field">
                                <label for="slug">نامک مقاله</label>

                                <input
                                    id="slug"
                                    name="slug"
                                    type="text"
                                    maxlength="191"
                                    dir="ltr"
                                    value="<?= ac_old_value($old, 'slug') ?>"
                                    placeholder="article-slug"
                                >

                                <span class="hint">
                                    فقط حروف، اعداد و خط تیره استفاده شود.
                                </span>
                            </div>

                            <div class="field">
                                <label for="excerpt">خلاصه مقاله</label>

                                <textarea
                                    id="excerpt"
                                    name="excerpt"
                                    maxlength="500"
                                    placeholder="خلاصه کوتاه مقاله..."
                                ><?= ac_old_value($old, 'excerpt') ?></textarea>
                            </div>

                        </div>

                        <div class="builder">

                            <div class="builder-header">
                                <div>
                                    <h2>محتوای مقاله</h2>
                                    <p>
                                        محتوای انتخاب‌شده در ستون body ذخیره می‌شود.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="button button-primary"
                                    id="add-first-block"
                                >
                                    افزودن المان
                                </button>
                            </div>

                            <div class="builder-content">

                                <aside class="toolbox">

                                    <div class="toolbox-title">
                                        المان‌ها
                                    </div>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="paragraph"
                                    >
                                        <span class="tool-icon">¶</span>
                                        متن
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="heading"
                                    >
                                        <span class="tool-icon">H</span>
                                        تیتر
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="image"
                                    >
                                        <span class="tool-icon">▧</span>
                                        تصویر
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="quote"
                                    >
                                        <span class="tool-icon">❝</span>
                                        نقل‌قول
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="video"
                                    >
                                        <span class="tool-icon">▶</span>
                                        ویدئو
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="button"
                                    >
                                        <span class="tool-icon">↗</span>
                                        دکمه
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="alert"
                                    >
                                        <span class="tool-icon">!</span>
                                        هشدار
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="code"
                                    >
                                        <span class="tool-icon">&lt;/&gt;</span>
                                        کد
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="faq"
                                    >
                                        <span class="tool-icon">?</span>
                                        FAQ
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="divider"
                                    >
                                        <span class="tool-icon">—</span>
                                        جداکننده
                                    </button>

                                    <button
                                        type="button"
                                        class="tool"
                                        data-add="spacer"
                                    >
                                        <span class="tool-icon">↕</span>
                                        فاصله
                                    </button>

                                </aside>

                                <div class="canvas">

                                    <div id="blocks" class="blocks"></div>

                                    <div id="empty" class="empty">
                                        <strong>محتوای مقاله خالی است</strong>
                                        از فهرست سمت راست یک المان اضافه کنید.
                                    </div>

                                </div>

                            </div>

                        </div>

                        <input
                            type="hidden"
                            name="content_blocks"
                            id="content_blocks"
                            value="<?= ac_old_value($old, 'content_blocks') ?>"
                        >

                        <div class="actions">
                            <a
                                class="button button-secondary"
                                href="articles.php"
                            >
                                انصراف
                            </a>

                            <button
                                class="button button-primary"
                                type="submit"
                            >
                                ذخیره مقاله
                            </button>
                        </div>

                    </section>

                    <aside>

                        <div class="card">

                            <h2>تنظیمات انتشار</h2>

                            <div class="field">
                                <label for="status">وضعیت مقاله</label>

                                <select id="status" name="status">
                                    <option
                                        value="draft"
                                        <?= $old['status'] === 'draft' ? 'selected' : '' ?>
                                    >
                                        پیش‌نویس
                                    </option>

                                    <option
                                        value="scheduled"
                                        <?= $old['status'] === 'scheduled' ? 'selected' : '' ?>
                                    >
                                        زمان‌بندی‌شده
                                    </option>

                                    <option
                                        value="published"
                                        <?= $old['status'] === 'published' ? 'selected' : '' ?>
                                    >
                                        منتشرشده
                                    </option>

                                    <option
                                        value="archived"
                                        <?= $old['status'] === 'archived' ? 'selected' : '' ?>
                                    >
                                        بایگانی‌شده
                                    </option>
                                </select>
                            </div>

                            <div class="field" id="published-at-field">
                                <label for="published_at">
                                    تاریخ انتشار زمان‌بندی‌شده
                                </label>

                                <input
                                    id="published_at"
                                    name="published_at"
                                    type="datetime-local"
                                    value="<?= ac_old_value($old, 'published_at') ?>"
                                >

                                <span class="hint">
                                    فقط برای وضعیت زمان‌بندی‌شده استفاده می‌شود.
                                </span>
                            </div>

                            <div class="field">
                                <label for="cover_image">آدرس تصویر کاور</label>

                                <input
                                    id="cover_image"
                                    name="cover_image"
                                    type="url"
                                    dir="ltr"
                                    maxlength="500"
                                    value="<?= ac_old_value($old, 'cover_image') ?>"
                                    placeholder="https://example.com/image.jpg"
                                >
                            </div>

                            <div class="field">
                                <label for="cover_alt">متن جایگزین تصویر</label>

                                <input
                                    id="cover_alt"
                                    name="cover_alt"
                                    type="text"
                                    maxlength="255"
                                    value="<?= ac_old_value($old, 'cover_alt') ?>"
                                    placeholder="توضیح تصویر"
                                >
                            </div>

                        </div>

                        <div class="card">

                            <h2>تنظیمات سئو</h2>

                            <div class="field">
                                <label for="seo_title">عنوان سئو</label>

                                <input
                                    id="seo_title"
                                    name="seo_title"
                                    type="text"
                                    maxlength="255"
                                    value="<?= ac_old_value($old, 'seo_title') ?>"
                                    placeholder="عنوان نمایش‌داده‌شده در گوگل"
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
                                    placeholder="توضیح کوتاه برای نتایج گوگل"
                                ><?= ac_old_value($old, 'meta_description') ?></textarea>
                            </div>

                            <div class="field">
                                <label for="focus_keyword">
                                    کلمه کلیدی اصلی
                                </label>

                                <input
                                    id="focus_keyword"
                                    name="focus_keyword"
                                    type="text"
                                    maxlength="150"
                                    value="<?= ac_old_value($old, 'focus_keyword') ?>"
                                    placeholder="مثلاً آموزش سئو"
                                >
                            </div>

                            <div class="field">
                                <label for="canonical_url">آدرس canonical</label>

                                <input
                                    id="canonical_url"
                                    name="canonical_url"
                                    type="url"
                                    dir="ltr"
                                    maxlength="2048"
                                    value="<?= ac_old_value($old, 'canonical_url') ?>"
                                    placeholder="https://example.com/article"
                                >
                            </div>

                            <label class="switch-row">
                                <input
                                    type="checkbox"
                                    name="robots_index"
                                    value="1"
                                    <?= $old['robots_index'] !== '0' ? 'checked' : '' ?>
                                >
                                اجازه ایندکس در موتورهای جست‌وجو
                            </label>

                            <label class="switch-row">
                                <input
                                    type="checkbox"
                                    name="robots_follow"
                                    value="1"
                                    <?= $old['robots_follow'] !== '0' ? 'checked' : '' ?>
                                >
                                اجازه دنبال‌کردن لینک‌ها
                            </label>

                        </div>

                    </aside>

                </div>

            </form>

        </main>

    </div>

</div>

<script>
(function () {
    'use strict';

    const form = document.getElementById('article-form');
    const blocksElement = document.getElementById('blocks');
    const emptyElement = document.getElementById('empty');
    const hiddenBlocks = document.getElementById('content_blocks');
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const statusInput = document.getElementById('status');
    const publishedAtField = document.getElementById('published-at-field');
    const addFirstBlock = document.getElementById('add-first-block');

    let blocks = [];

    try {
        blocks = JSON.parse(hiddenBlocks.value || '[]');

        if (!Array.isArray(blocks)) {
            blocks = [];
        }
    } catch (error) {
        blocks = [];
    }

    function makeId() {
        return 'block_' + Date.now() + '_' + Math.random()
            .toString(36)
            .substring(2, 9);
    }

    function newBlock(type) {
        const defaults = {
            paragraph: {
                html: ''
            },
            heading: {
                level: 'h2',
                text: ''
            },
            image: {
                url: '',
                alt: '',
                caption: ''
            },
            quote: {
                text: '',
                author: ''
            },
            video: {
                url: '',
                title: ''
            },
            button: {
                text: 'مشاهده بیشتر',
                url: '',
                style: 'primary'
            },
            alert: {
                title: 'نکته مهم',
                text: '',
                alert_type: 'info'
            },
            code: {
                language: 'text',
                code: ''
            },
            faq: {
                items: [
                    {
                        question: '',
                        answer: ''
                    }
                ]
            },
            divider: {
                style: 'solid'
            },
            spacer: {
                height: 40
            }
        };

        return {
            id: makeId(),
            type: type,
            data: defaults[type] || {}
        };
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function blockName(type) {
        const names = {
            paragraph: 'متن',
            heading: 'تیتر',
            image: 'تصویر',
            quote: 'نقل‌قول',
            video: 'ویدئو',
            button: 'دکمه',
            alert: 'هشدار',
            code: 'کد',
            faq: 'سؤالات متداول',
            divider: 'جداکننده',
            spacer: 'فاصله'
        };

        return names[type] || 'المان';
    }

    function input(label, key, value, type = 'text') {
        return `
            <label>${label}</label>
            <input
                type="${type}"
                data-key="${key}"
                value="${escapeHtml(value)}"
            >
        `;
    }

    function textarea(label, key, value) {
        return `
            <label>${label}</label>
            <textarea data-key="${key}">${escapeHtml(value)}</textarea>
        `;
    }

    function renderBlock(block, index) {
        const data = block.data || {};
        let fields = '';

        if (block.type === 'paragraph') {
            fields = textarea('متن HTML مقاله', 'html', data.html || '');
        }

        if (block.type === 'heading') {
            fields = `
                <label>سطح تیتر</label>
                <select data-key="level">
                    <option value="h2" ${data.level === 'h2' ? 'selected' : ''}>H2</option>
                    <option value="h3" ${data.level === 'h3' ? 'selected' : ''}>H3</option>
                    <option value="h4" ${data.level === 'h4' ? 'selected' : ''}>H4</option>
                </select>
                ${input('متن تیتر', 'text', data.text || '')}
            `;
        }

        if (block.type === 'image') {
            fields =
                input('آدرس تصویر', 'url', data.url || '','url') +
                input('متن جایگزین', 'alt', data.alt || '') +
                input('عنوان تصویر', 'caption', data.caption || '');
        }

        if (block.type === 'quote') {
            fields =
                textarea('متن نقل‌قول', 'text', data.text || '') +
                input('نام نویسنده', 'author', data.author || '');
        }

        if (block.type === 'video') {
            fields =
                input('آدرس ویدئو', 'url', data.url || '', 'url') +
                input('عنوان ویدئو', 'title', data.title || '');
        }

        if (block.type === 'button') {
            fields =
                input('متن دکمه', 'text', data.text || '') +
                input('آدرس دکمه', 'url', data.url || '', 'url') +
                `
                <label>رنگ دکمه</label>
                <select data-key="style">
                    <option value="primary" ${data.style === 'primary' ? 'selected' : ''}>اصلی</option>
                    <option value="secondary" ${data.style === 'secondary' ? 'selected' : ''}>ثانویه</option>
                </select>
                `;
        }

        if (block.type === 'alert') {
            fields =
                input('عنوان', 'title', data.title || '') +
                textarea('متن هشدار', 'text', data.text || '') +
                `
                <label>نوع هشدار</label>
                <select data-key="alert_type">
                    <option value="info" ${data.alert_type === 'info' ? 'selected' : ''}>اطلاعات</option>
                    <option value="success" ${data.alert_type === 'success' ? 'selected' : ''}>موفقیت</option>
                    <option value="warning" ${data.alert_type === 'warning' ? 'selected' : ''}>هشدار</option>
                    <option value="danger" ${data.alert_type === 'danger' ? 'selected' : ''}>خطر</option>
                </select>
                `;
        }

        if (block.type === 'code') {
            fields =
                input('زبان برنامه‌نویسی', 'language', data.language || 'text') +
                textarea('کد', 'code', data.code || '');
        }

        if (block.type === 'faq') {
            const items = Array.isArray(data.items) ? data.items : [];

            fields = items.map((item, itemIndex) => `
                <div class="faq-item">
                    ${input('سؤال', `faq_question_${itemIndex}`, item.question || '')}
                    ${textarea('پاسخ', `faq_answer_${itemIndex}`, item.answer || '')}
                    <button type="button" data-remove-faq="${itemIndex}">
                        حذف این سؤال
                    </button>
                </div>
            `).join('');

            fields += `
                <button type="button" data-add-faq>
                    افزودن سؤال جدید
                </button>
            `;
        }

        if (block.type === 'divider') {
            fields = `
                <label>نوع خط</label>
                <select data-key="style">
                    <option value="solid" ${data.style === 'solid' ? 'selected' : ''}>پیوسته</option>
                    <option value="dashed" ${data.style === 'dashed' ? 'selected' : ''}>خط‌چین</option>
                    <option value="dotted" ${data.style === 'dotted' ? 'selected' : ''}>نقطه‌چین</option>
                </select>
            `;
        }

        if (block.type === 'spacer') {
            fields = input(
                'ارتفاع فاصله بر حسب پیکسل',
                'height',
                data.height || 40,
                'number'
            );
        }

        return `
            <div class="block" data-index="${index}">
                <div class="block-head">
                    <span class="block-title">${blockName(block.type)}</span>

                    <div class="block-actions">
                        <button type="button" data-up title="انتقال به بالا">↑</button>
                        <button type="button" data-down title="انتقال به پایین">↓</button>
                        <button type="button" data-delete title="حذف">×</button>
                    </div>
                </div>

                <div class="block-body">
                    ${fields}
                </div>
            </div>
        `;
    }

    function sync() {
        hiddenBlocks.value = JSON.stringify(blocks);
        emptyElement.style.display = blocks.length ? 'none' : 'block';
    }

    function render() {
        blocksElement.innerHTML = blocks
            .map((block, index) => renderBlock(block, index))
            .join('');

        sync();
    }

    function addBlock(type) {
        blocks.push(newBlock(type));
        render();

        const lastBlock = blocksElement.lastElementChild;

        if (lastBlock) {
            lastBlock.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    document.querySelectorAll('[data-add]').forEach(function (button) {
        button.addEventListener('click', function () {
            addBlock(button.dataset.add);
        });
    });

    addFirstBlock.addEventListener('click', function () {
        addBlock('paragraph');
    });

    blocksElement.addEventListener('input', function (event) {
        const target = event.target;
        const blockElement = target.closest('.block');

        if (!blockElement) {
            return;
        }

        const index = Number(blockElement.dataset.index);
        const block = blocks[index];

        if (!block) {
            return;
        }

        if (target.dataset.key) {
            let value = target.value;

            if (target.dataset.key === 'height') {
                value = Number(value || 40);
            }

            block.data[target.dataset.key] = value;
        }

        if (target.dataset.key && target.dataset.key.startsWith('faq_')) {
            const parts = target.dataset.key.split('_');
            const field = parts[1];
            const itemIndex = Number(parts[2]);

            if (block.data.items[itemIndex]) {
                block.data.items[itemIndex][field] = target.value;
            }
        }

        sync();
    });

    blocksElement.addEventListener('change', function (event) {
        const target = event.target;
        const blockElement = target.closest('.block');

        if (!blockElement || !target.dataset.key) {
            return;
        }

        const index = Number(blockElement.dataset.index);

        if (blocks[index]) {
            blocks[index].data[target.dataset.key] = target.value;
            sync();
        }
    });

    blocksElement.addEventListener('click', function (event) {
        const target = event.target;
        const blockElement = target.closest('.block');

        if (!blockElement) {
            return;
        }

        const index = Number(blockElement.dataset.index);

        if (target.dataset.delete !== undefined) {
            blocks.splice(index, 1);
            render();
            return;
        }

        if (target.dataset.up !== undefined && index > 0) {
            [blocks[index - 1], blocks[index]] =
                [blocks[index], blocks[index - 1]];

            render();
            return;
        }

        if (
            target.dataset.down !== undefined &&
            index < blocks.length - 1
        ) {
            [blocks[index], blocks[index + 1]] =
                [blocks[index + 1], blocks[index]];

            render();
            return;
        }

        if (target.dataset.addFaq !== undefined) {
            blocks[index].data.items.push({
                question: '',
                answer: ''
            });

            render();
            return;
        }

        if (target.dataset.removeFaq !== undefined) {
            const faqIndex = Number(target.dataset.removeFaq);

            blocks[index].data.items.splice(faqIndex, 1);

            render();
        }
    });

    function updatePublishedAtVisibility() {
        publishedAtField.style.display =
            statusInput.value === 'scheduled'
                ? 'block'
                : 'none';
    }

    statusInput.addEventListener(
        'change',
        updatePublishedAtVisibility
    );

    titleInput.addEventListener('input', function () {
        if (slugInput.dataset.manuallyChanged !== '1') {
            slugInput.value = titleInput.value
                .trim()
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\u0600-\u06ff-]+/g, '')
                .replace(/-+/g, '-');
        }
    });

    slugInput.addEventListener('input', function () {
        slugInput.dataset.manuallyChanged = '1';
    });

    form.addEventListener('submit', function () {
        sync();
    });

    updatePublishedAtVisibility();
    render();
})();
</script>

</body>
</html>