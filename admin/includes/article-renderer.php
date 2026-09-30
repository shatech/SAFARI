<?php

declare(strict_types=1);

function render_article_blocks(string|null $json): string
{
    $blocks = json_decode((string) $json, true);

    if (!is_array($blocks)) {
        return '';
    }

    $output = '';

    foreach ($blocks as $block) {
        if (!is_array($block)) {
            continue;
        }

        $type = (string) ($block['type'] ?? '');
        $data = is_array($block['data'] ?? null)
            ? $block['data']
            : [];

        $output .= render_article_block($type, $data);
    }

    return $output;
}

function render_article_block(string $type, array $data): string
{
    switch ($type) {
        case 'paragraph':
            return '<div class="article-block article-paragraph">'
                . sanitize_rendered_html((string) ($data['html'] ?? ''))
                . '</div>';

        case 'heading':
            $level = in_array(
                ($data['level'] ?? ''),
                ['h2', 'h3', 'h4'],
                true
            )
                ? $data['level']
                : 'h2';

            return sprintf(
                '<%1$s class="article-block article-heading">%2$s</%1$s>',
                $level,
                e((string) ($data['text'] ?? ''))
            );

        case 'image':
            $url = e((string) ($data['url'] ?? ''));

            if ($url === '') {
                return '';
            }

            return '
                <figure class="article-block article-image">
                    <img
                        src="' . $url . '"
                        alt="' . e((string) ($data['alt'] ?? '')) . '"
                        loading="lazy"
                    >
                    ' . (
                        !empty($data['caption'])
                            ? '<figcaption>' . e((string) $data['caption']) . '</figcaption>'
                            : ''
                    ) . '
                </figure>
            ';

        case 'quote':
            return '
                <blockquote class="article-block article-quote">
                    <p>' . e((string) ($data['text'] ?? '')) . '</p>
                    ' . (
                        !empty($data['author'])
                            ? '<cite>' . e((string) $data['author']) . '</cite>'
                            : ''
                    ) . '
                </blockquote>
            ';

        case 'video':
            $url = e((string) ($data['url'] ?? ''));

            if ($url === '') {
                return '';
            }

            return '
                <div class="article-block article-video">
                    <a
                        href="' . $url . '"
                        target="_blank"
                        rel="noopener nofollow"
                    >
                        مشاهده ویدئو
                    </a>
                </div>
            ';

        case 'button':
            $url = e((string) ($data['url'] ?? ''));

            if ($url === '') {
                return '';
            }

            $style = in_array(
                ($data['style'] ?? ''),
                ['primary', 'secondary', 'success'],
                true
            )
                ? $data['style']
                : 'primary';

            $align = in_array(
                ($data['align'] ?? ''),
                ['right', 'center', 'left'],
                true
            )
                ? $data['align']
                : 'right';

            return '
                <div class="article-block article-button-wrap text-' . $align . '">
                    <a
                        class="article-button article-button-' . $style . '"
                        href="' . $url . '"
                    >
                        ' . e((string) ($data['text'] ?? 'مشاهده بیشتر')) . '
                    </a>
                </div>
            ';

        case 'alert':
            $alertType = in_array(
                ($data['type'] ?? ''),
                ['info', 'success', 'warning', 'danger'],
                true
            )
                ? $data['type']
                : 'info';

            return '
                <div class="article-block article-alert alert-' . $alertType . '">
                    <strong>' . e((string) ($data['title'] ?? '')) . '</strong>
                    <p>' . e((string) ($data['text'] ?? '')) . '</p>
                </div>
            ';

        case 'divider':
            $style = in_array(
                ($data['style'] ?? ''),
                ['solid', 'dashed', 'dotted'],
                true
            )
                ? $data['style']
                : 'solid';

            return '<hr class="article-block article-divider divider-' . $style . '">';

        case 'spacer':
            $height = max(
                10,
                min(300, (int) ($data['height'] ?? 40))
            );

            return '<div class="article-block article-spacer" style="height:'
                . $height
                . 'px"></div>';

        case 'columns':
            return '
                <div class="article-block article-columns">
                    <div>' . sanitize_rendered_html((string) ($data['left'] ?? '')) . '</div>
                    <div>' . sanitize_rendered_html((string) ($data['right'] ?? '')) . '</div>
                </div>
            ';

        case 'code':
            return '
                <pre class="article-block article-code"><code>'
                . e((string) ($data['code'] ?? ''))
                . '</code></pre>
            ';

        case 'faq':
            $html = '<section class="article-block article-faq">';

            foreach (($data['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $question = trim((string) ($item['question'] ?? ''));
                $answer = (string) ($item['answer'] ?? '');

                if ($question === '') {
                    continue;
                }

                $html .= '
                    <details>
                        <summary>' . e($question) . '</summary>
                        <div>' . sanitize_rendered_html($answer) . '</div>
                    </details>
                ';
            }

            return $html . '</section>';

        case 'poll':
            $html = '
                <section class="article-block article-poll">
                    <h3>' . e((string) ($data['question'] ?? '')) . '</h3>
                    <form method="post" action="poll-vote.php">
            ';

            foreach (($data['options'] ?? []) as $index => $option) {
                $html .= '
                    <label class="poll-option">
                        <input
                            type="radio"
                            name="option"
                            value="' . (int) $index . '"
                        >
                        <span>' . e((string) $option) . '</span>
                    </label>
                ';
            }

            return $html . '
                        <button type="submit">ثبت رأی</button>
                    </form>
                </section>
            ';

        case 'gallery':
            $html = '<div class="article-block article-gallery">';

            foreach (($data['images'] ?? []) as $image) {
                if (!is_array($image)) {
                    continue;
                }

                $url = trim((string) ($image['url'] ?? ''));

                if ($url === '') {
                    continue;
                }

                $html .= '
                    <img
                        src="' . e($url) . '"
                        alt="' . e((string) ($image['alt'] ?? '')) . '"
                        loading="lazy"
                    >
                ';
            }

            return $html . '</div>';
    }

    return '';
}

function sanitize_rendered_html(string $html): string
{
    return strip_tags(
        $html,
        '<p><br><strong><b><em><i><u><s><blockquote><ul><ol><li><h2><h3><h4><a><code><pre>'
    );
}