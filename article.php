<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| article.php
|--------------------------------------------------------------------------
| نمایش مقاله بر اساس slug:
|
| article.php?slug=example-slug
|
| ساختار مورد انتظار جدول articles:
| id
| title
| slug
| excerpt
| content
| content_blocks
| featured_image
| status
| published_at
| category
| category_name
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/config/database.php';

if (!function_exists('db')) {
    http_response_code(500);
    exit('تابع اتصال به دیتابیس پیدا نشد.');
}

$pdo = db();

/*
|--------------------------------------------------------------------------
| توابع کمکی
|--------------------------------------------------------------------------
*/

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function article_value(array $article, array $keys, mixed $default = ''): mixed
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $article) && $article[$key] !== null) {
            return $article[$key];
        }
    }

    return $default;
}

function normalize_asset_url(?string $path): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    // آدرس‌های کامل مانند https://example.com/image.jpg
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }

    // جلوگیری از مسیرهای خطرناک
    if (str_starts_with($path, 'javascript:') || str_starts_with($path, 'data:')) {
        return '';
    }

    // اگر مسیر از قبل با / شروع شده باشد
    if (str_starts_with($path, '/')) {
        return $path;
    }

    return '/' . ltrim($path, '/');
}

function format_article_date(mixed $date): string
{
    if (!$date) {
        return '';
    }

    try {
        $dateObject = new DateTime((string) $date);
        return $dateObject->format('Y/m/d');
    } catch (Throwable) {
        return (string) $date;
    }
}

function safe_rich_text(mixed $html): string
{
    /*
     * محتوای HTML که از پنل مدیریت ذخیره شده است.
     * در صورت استفاده عمومی، بهتر است HTMLPurifier نیز نصب شود.
     */
    return strip_tags(
        (string) $html,
        '<p><br><strong><b><em><i><u><s><ul><ol><li><a><span>'
    );
}

function render_article_block(mixed $block): string
{
    if (is_string($block)) {
        return '<div class="article-paragraph">' . nl2br(e($block)) . '</div>';
    }

    if (!is_array($block)) {
        return '';
    }

    $type = strtolower((string) (
        $block['type']
        ?? $block['block_type']
        ?? $block['element']
        ?? ''
    ));

    $data = $block['data'] ?? $block['content'] ?? $block;

    if (!is_array($data)) {
        $data = ['value' => $data];
    }

    $text = $data['text']
        ?? $data['value']
        ?? $data['content']
        ?? '';

    $title = $data['title']
        ?? $data['heading']
        ?? $data['question']
        ?? '';

    $image = $data['image']
        ?? $data['src']
        ?? $data['url']
        ?? '';

    if (is_array($image)) {
        $image = $image['url']
            ?? $image['src']
            ?? $image['path']
            ?? '';
    }

    switch ($type) {
        case 'paragraph':
        case 'text':
        case 'richtext':
        case 'rich_text':
            return '<div class="article-paragraph">'
                . safe_rich_text($text)
                . '</div>';

        case 'heading':
        case 'title':
        case 'h2':
            $level = (int) ($data['level'] ?? 2);
            $level = max(2, min(4, $level));

            return '<h' . $level . ' class="article-heading">'
                . e($title ?: $text)
                . '</h' . $level . '>';

        case 'image':
            $imageUrl = normalize_asset_url((string) $image);

            if ($imageUrl === '') {
                return '';
            }

            $caption = $data['caption']
                ?? $data['alt']
                ?? '';

            return '<figure class="article-image">'
                . '<img src="' . e($imageUrl) . '" alt="' . e($caption) . '" loading="lazy">'
                . ($caption !== ''
                    ? '<figcaption>' . e($caption) . '</figcaption>'
                    : '')
                . '</figure>';

        case 'gallery':
        case 'images':
            $images = $data['images'] ?? $data['items'] ?? [];

            if (!is_array($images) || count($images) === 0) {
                return '';
            }

            $html = '<div class="article-gallery">';

            foreach ($images as $galleryImage) {
                if (is_array($galleryImage)) {
                    $galleryImage = $galleryImage['url']
                        ?? $galleryImage['src']
                        ?? $galleryImage['path']
                        ?? '';
                }

                $imageUrl = normalize_asset_url((string) $galleryImage);

                if ($imageUrl !== '') {
                    $html .= '<img src="' . e($imageUrl) . '" alt="" loading="lazy">';
                }
            }

            $html .= '</div>';

            return $html;

        case 'quote':
        case 'blockquote':
            $author = $data['author']
                ?? $data['cite']
                ?? $data['source']
                ?? '';

            return '<blockquote class="article-quote">'
                . '<p>' . safe_rich_text($text) . '</p>'
                . ($author !== ''
                    ? '<cite>' . e($author) . '</cite>'
                    : '')
                . '</blockquote>';

        case 'video':
            $videoUrl = $data['video_url']
                ?? $data['url']
                ?? $data['src']
                ?? '';

            if ($videoUrl === '') {
                return '';
            }

            $safeVideoUrl = filter_var($videoUrl, FILTER_VALIDATE_URL);

            if (!$safeVideoUrl) {
                return '';
            }

            return '<div class="article-video">'
                . '<iframe src="' . e($safeVideoUrl) . '" '
                . 'title="' . e($title ?: 'ویدئوی مقاله') . '" '
                . 'loading="lazy" frameborder="0" allowfullscreen>'
                . '</iframe>'
                . '</div>';

        case 'button':
        case 'link':
            $buttonUrl = $data['url']
                ?? $data['href']
                ?? '#';

            $buttonText = $data['label']
                ?? $data['text']
                ?? $data['title']
                ?? 'مشاهده بیشتر';

            $style = $data['style']
                ?? $data['variant']
                ?? 'primary';

            $allowedStyles = [
                'primary',
                'secondary',
                'success'
            ];

            if (!in_array($style, $allowedStyles, true)) {
                $style = 'primary';
            }

            return '<div class="article-button-wrap">'
                . '<a class="article-button article-button-' . e($style) . '" '
                . 'href="' . e((string) $buttonUrl) . '">'
                . e($buttonText)
                . '</a>'
                . '</div>';

        case 'alert':
        case 'notice':
            $alertType = $data['alert_type']
                ?? $data['style']
                ?? 'info';

            $allowedTypes = [
                'info',
                'success',
                'warning',
                'danger'
            ];

            if (!in_array($alertType, $allowedTypes, true)) {
                $alertType = 'info';
            }

            return '<div class="article-alert alert-' . e($alertType) . '">'
                . ($title !== '' ? '<strong>' . e($title) . '</strong>' : '')
                . '<p>' . safe_rich_text($text) . '</p>'
                . '</div>';

        case 'divider':
        case 'separator':
            return '<hr class="article-divider">';

        case 'code':
            return '<pre class="article-code"><code>'
                . e($data['code'] ?? $text)
                . '</code></pre>';

        case 'faq':
            $items = $data['items']
                ?? $data['questions']
                ?? [];

            if (!is_array($items) || count($items) === 0) {
                return '';
            }

            $html = '<div class="article-faq">';

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $question = $item['question']
                    ?? $item['title']
                    ?? '';

                $answer = $item['answer']
                    ?? $item['text']
                    ?? $item['content']
                    ?? '';

                if ($question === '') {
                    continue;
                }

                $html .= '<details>'
                    . '<summary>' . e($question) . '</summary>'
                    . '<div>' . safe_rich_text($answer) . '</div>'
                    . '</details>';
            }

            $html .= '</div>';

            return $html;

        case 'columns':
        case 'two_columns':
            $columns = $data['columns']
                ?? $data['items']
                ?? [];

            if (!is_array($columns) || count($columns) === 0) {
                return '';
            }

            $html = '<div class="article-columns">';

            foreach ($columns as $column) {
                if (is_array($column)) {
                    $column = $column['content']
                        ?? $column['text']
                        ?? '';
                }

                $html .= '<div>' . safe_rich_text($column) . '</div>';
            }

            $html .= '</div>';

            return $html;

        case 'poll':
        case 'survey':
            $question = $data['question']
                ?? $data['title']
                ?? 'نظر شما چیست؟';

            $options = $data['options'] ?? [];

            if (!is_array($options) || count($options) === 0) {
                return '';
            }

            $html = '<div class="article-poll">'
                . '<strong>' . e($question) . '</strong>';

            foreach ($options as $index => $option) {
                if (is_array($option)) {
                    $option = $option['label']
                        ?? $option['text']
                        ?? '';
                }

                $html .= '<label class="poll-option">'
                    . '<input type="radio" name="article_poll" value="' . e((string) $index) . '">'
                    . '<span>' . e($option) . '</span>'
                    . '</label>';
            }

            $html .= '<button type="button" onclick="alert(\'نتیجه نظرسنجی در نسخه بعدی فعال می‌شود.\')">'
                . 'ثبت نظر'
                . '</button>'
                . '</div>';

            return $html;

        default:
            /*
             * برای بلاک‌های ناشناخته، اگر متن وجود داشته باشد آن را نمایش می‌دهیم.
             */
            if ($text !== '') {
                return '<div class="article-paragraph">'
                    . safe_rich_text($text)
                    . '</div>';
            }

            return '';
    }
}

/*
|--------------------------------------------------------------------------
| دریافت slug
|--------------------------------------------------------------------------
*/

$slug = trim((string) ($_GET['slug'] ?? ''));

if ($slug === '') {
    http_response_code(404);
    $pageTitle = 'مقاله پیدا نشد';
    require_once __DIR__ . '/includes/header.php';
    ?>

    <main class="section">
        <div class="container">
            <div class="article-content text-center">
                <h1 class="article-heading">مقاله پیدا نشد</h1>
                <p class="article-paragraph">
                    برای مشاهده مقاله، نشانی صفحه کامل نیست.
                </p>
                <a href="/" class="article-button article-button-primary">
                    بازگشت به صفحه اصلی
                </a>
            </div>
        </div>
    </main>

    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| دریافت مقاله
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    "SELECT *
     FROM articles
     WHERE slug = :slug
       AND (
           status = 'published'
           OR status = 1
           OR status IS NULL
       )
     LIMIT 1"
);

$stmt->execute([
    ':slug' => $slug
]);

$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    http_response_code(404);
    $pageTitle = 'مقاله پیدا نشد';

    require_once __DIR__ . '/includes/header.php';
    ?>

    <main class="section">
        <div class="container">
            <div class="article-content text-center">
                <h1 class="article-heading">مقاله موردنظر پیدا نشد</h1>
                <p class="article-paragraph">
                    ممکن است مقاله حذف شده باشد یا هنوز منتشر نشده باشد.
                </p>
                <a href="/articles.php" class="article-button article-button-primary">
                    مشاهده همه مقالات
                </a>
            </div>
        </div>
    </main>

    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| اطلاعات مقاله
|--------------------------------------------------------------------------
*/

$title = article_value($article, ['title', 'name'], 'بدون عنوان');

$excerpt = article_value(
    $article,
    ['excerpt', 'summary', 'description'],
    ''
);

$category = article_value(
    $article,
    ['category_name', 'category', 'category_title'],
    'مقالات'
);

$publishedAt = article_value(
    $article,
    ['published_at', 'created_at', 'date'],
    ''
);

$featuredImage = article_value(
    $article,
    ['featured_image', 'cover_image', 'image', 'thumbnail'],
    ''
);

$featuredImage = normalize_asset_url((string) $featuredImage);

/*
|--------------------------------------------------------------------------
| آماده‌سازی محتوای مقاله
|--------------------------------------------------------------------------
*/

$contentBlocks = article_value($article, ['content_blocks'], '');

$blocks = [];

if (is_array($contentBlocks)) {
    $blocks = $contentBlocks;
} elseif (is_string($contentBlocks) && trim($contentBlocks) !== '') {
    $decodedBlocks = json_decode($contentBlocks, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedBlocks)) {
        $blocks = $decodedBlocks;
    }
}

/*
 * پشتیبانی از مقاله‌های قدیمی که content_blocks ندارند
 */
if (count($blocks) === 0) {
    $oldContent = article_value($article, ['content'], '');

    if ($oldContent !== '') {
        $blocks = [
            [
                'type' => 'richtext',
                'data' => [
                    'text' => $oldContent
                ]
            ]
        ];
    }
}

$renderedContent = '';

foreach ($blocks as $block) {
    $renderedContent .= render_article_block($block);
}

if ($renderedContent === '') {
    $renderedContent = '<p class="article-paragraph">محتوای این مقاله هنوز ثبت نشده است.</p>';
}

$pageTitle = $title . ' | صفری سازه';

require_once __DIR__ . '/includes/header.php';
?>

<main>

    <!-- Article Hero -->
    <section class="hero-section article-hero-section">
        <div class="hero-orb hero-orb--two"></div>

        <div class="container hero-container">
            <div class="row align-items-center g-4">

                <div class="col-lg-7">
                    <div class="hero-content">

                        <div class="eyebrow eyebrow--light">
                            <span class="eyebrow-dot"></span>
                            <?= e($category) ?>
                        </div>

                        <h1>
                            <?= e($title) ?>
                        </h1>

                        <?php if ($excerpt !== ''): ?>
                            <p class="hero-description">
                                <?= e($excerpt) ?>
                            </p>
                        <?php endif; ?>

                        <div class="hero-note">
                            <i class="fa-regular fa-calendar"></i>
                            <span>
                                <?php if ($publishedAt !== ''): ?>
                                    تاریخ انتشار:
                                    <?= e(format_article_date($publishedAt)) ?>
                                <?php else: ?>
                                    مقاله تخصصی صفری سازه
                                <?php endif; ?>
                            </span>
                        </div>

                    </div>
                </div>

                <?php if ($featuredImage !== ''): ?>
                    <div class="col-lg-5">
                        <div class="hero-visual">
                            <div class="hero-image-wrap">
                                <img
                                    class="hero-image"
                                    src="<?= e($featuredImage) ?>"
                                    alt="<?= e($title) ?>"
                                    loading="eager"
                                >

                                <div class="hero-image-shade"></div>

                                <div class="hero-image-caption">
                                    <div class="caption-icon">
                                        <i class="fa-regular fa-file-lines"></i>
                                    </div>

                                    <div>
                                        <strong><?= e($category) ?></strong>
                                        <small>دانشنامه صفری سازه</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="hero-bottom-line"></div>
    </section>

    <!-- Breadcrumb -->
    <section class="quick-guide-section article-meta-section">
        <div class="container">
            <nav aria-label="مسیر صفحه">
                <ol class="breadcrumb mb-0" style="font-size: 12px;">
                    <li class="breadcrumb-item">
                        <a href="/">صفحه اصلی</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/articles.php">مقالات</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= e($title) ?>
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Article Content -->
    <section class="section article-section">
        <div class="container">
            <article class="article-content">

                <?php if ($excerpt !== ''): ?>
                    <div class="article-intro">
                        <p class="article-paragraph">
                            <?= e($excerpt) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?= $renderedContent ?>

            </article>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<style>
    .article-hero-section {
        min-height: auto;
        padding: 82px 0 90px;
    }

    .article-hero-section h1 {
        max-width: 760px;
        font-size: clamp(32px, 4.5vw, 58px);
        line-height: 1.55;
    }

    .article-meta-section {
        padding: 22px 0;
        border-bottom: 1px solid var(--line);
    }

    .article-meta-section .breadcrumb {
        color: var(--muted);
    }

    .article-meta-section a {
        color: var(--orange-dark);
        font-weight: 700;
    }

    .article-section {
        padding-top: 65px;
    }

    .article-intro {
        margin-bottom: 35px;
        padding: 22px 25px;
        border-right: 4px solid var(--orange);
        border-radius: 12px;
        background: #fff8f3;
        color: #657179;
    }

    .article-intro .article-paragraph {
        margin: 0;
    }

    .article-video iframe {
        width: 100%;
        min-height: 430px;
        border: 0;
        border-radius: 12px;
    }

    .article-columns > div {
        min-width: 0;
    }

    @media (max-width: 767.98px) {
        .article-hero-section {
            padding: 55px 0 70px;
        }

        .article-section {
            padding-top: 45px;
        }

        .article-video {
            padding: 15px;
        }

        .article-video iframe {
            min-height: 230px;
        }
    }
</style>