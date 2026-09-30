(() => {
    'use strict';

    const container = document.getElementById('blocks-container');
    const emptyState = document.getElementById('empty-builder');
    const hiddenInput = document.getElementById('content_blocks');

    const elementsPanel = document.getElementById('elements-panel');
    const openElements = document.getElementById('open-elements');
    const closeElements = document.getElementById('close-elements');
    const emptyAddElement = document.getElementById('empty-add-element');
    const searchInput = document.getElementById('element-search');

    let blocks = [];

    const labels = {
        paragraph: 'متن غنی',
        heading: 'تیتر بخش',
        image: 'تصویر',
        gallery: 'گالری تصاویر',
        quote: 'نقل‌قول',
        video: 'ویدئو',
        button: 'دکمه CTA',
        alert: 'باکس اطلاع‌رسانی',
        divider: 'جداکننده',
        spacer: 'فاصله‌دهنده',
        columns: 'دو ستون',
        code: 'کد برنامه‌نویسی',
        faq: 'سؤالات متداول',
        poll: 'نظرسنجی'
    };

    function uid() {
        return 'block_' + Date.now() + '_' + Math.random()
            .toString(36)
            .substring(2, 9);
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function defaultData(type) {
        const data = {
            paragraph: {
                html: '<p>متن خود را اینجا وارد کنید...</p>'
            },

            heading: {
                level: 'h2',
                text: 'عنوان بخش جدید'
            },

            image: {
                url: '',
                alt: '',
                caption: ''
            },

            gallery: {
                columns: 3,
                images: []
            },

            quote: {
                text: 'این بخش برای یک نقل‌قول مهم یا جمله کلیدی استفاده می‌شود.',
                author: ''
            },

            video: {
                url: '',
                title: ''
            },

            button: {
                text: 'مشاهده بیشتر',
                url: '',
                style: 'primary',
                align: 'right'
            },

            alert: {
                title: 'نکته مهم',
                text: 'متن اطلاع‌رسانی خود را وارد کنید.',
                type: 'info'
            },

            divider: {
                style: 'solid'
            },

            spacer: {
                height: 40
            },

            columns: {
                left: '<p>محتوای ستون اول...</p>',
                right: '<p>محتوای ستون دوم...</p>'
            },

            code: {
                language: 'php',
                code: '<?php echo "Hello World"; ?>'
            },

            faq: {
                items: [
                    {
                        question: 'سؤال متداول اول چیست؟',
                        answer: 'پاسخ سؤال را اینجا وارد کنید.'
                    }
                ]
            },

            poll: {
                question: 'نظر شما درباره این مقاله چیست؟',
                options: [
                    'عالی بود',
                    'خوب بود',
                    'نیاز به بهبود دارد'
                ]
            }
        };

        return JSON.parse(JSON.stringify(data[type] || {}));
    }

    function addBlock(type) {
        blocks.push({
            id: uid(),
            type: type,
            data: defaultData(type)
        });

        render();
        save();

        elementsPanel.classList.remove('open');
    }

    function deleteBlock(id) {
        if (!confirm('این المان حذف شود؟')) {
            return;
        }

        blocks = blocks.filter(block => block.id !== id);

        render();
        save();
    }

    function duplicateBlock(id) {
        const original = blocks.find(block => block.id === id);

        if (!original) {
            return;
        }

        const copy = JSON.parse(JSON.stringify(original));
        copy.id = uid();

        const index = blocks.findIndex(block => block.id === id);

        blocks.splice(index + 1, 0, copy);

        render();
        save();
    }

    function moveBlock(id, direction) {
        const index = blocks.findIndex(block => block.id === id);
        const newIndex = index + direction;

        if (index < 0 || newIndex < 0 || newIndex >= blocks.length) {
            return;
        }

        [blocks[index], blocks[newIndex]] = [blocks[newIndex], blocks[index]];

        render();
        save();
    }

    function field(label, input) {
        return `
            <div class="block-field">
                <label>${label}</label>
                ${input}
            </div>
        `;
    }

    function input(name, value, placeholder = '') {
        return `
            <input
                data-field="${name}"
                value="${escapeHtml(value)}"
                placeholder="${escapeHtml(placeholder)}"
            >
        `;
    }

    function textarea(name, value, placeholder = '') {
        return `
            <textarea
                data-field="${name}"
                placeholder="${escapeHtml(placeholder)}"
            >${escapeHtml(value)}</textarea>
        `;
    }

    function select(name, value, options) {
        return `
            <select data-field="${name}">
                ${options.map(option => `
                    <option
                        value="${escapeHtml(option.value)}"
                        ${option.value === value ? 'selected' : ''}
                    >
                        ${escapeHtml(option.label)}
                    </option>
                `).join('')}
            </select>
        `;
    }

    function renderEditor(block) {
        const data = block.data;

        switch (block.type) {
            case 'paragraph':
                return field(
                    'متن HTML مجاز',
                    textarea('html', data.html)
                );

            case 'heading':
                return `
                    ${field(
                        'سطح تیتر',
                        select('level', data.level, [
                            { value: 'h2', label: 'H2' },
                            { value: 'h3', label: 'H3' },
                            { value: 'h4', label: 'H4' }
                        ])
                    )}

                    ${field(
                        'متن تیتر',
                        input('text', data.text, 'عنوان بخش...')
                    )}
                `;

            case 'image':
                return `
                    ${field(
                        'آدرس تصویر',
                        input('url', data.url, 'https://example.com/image.jpg')
                    )}

                    ${field(
                        'متن جایگزین Alt',
                        input('alt', data.alt, 'توضیح تصویر برای سئو...')
                    )}

                    ${field(
                        'کپشن تصویر',
                        input('caption', data.caption, 'توضیح کوتاه زیر تصویر...')
                    )}
                `;

            case 'quote':
                return `
                    ${field(
                        'متن نقل‌قول',
                        textarea('text', data.text)
                    )}

                    ${field(
                        'نام نویسنده',
                        input('author', data.author, 'اختیاری')
                    )}
                `;

            case 'video':
                return `
                    ${field(
                        'آدرس ویدئو',
                        input('url', data.url, 'https://youtube.com/watch?v=...')
                    )}

                    ${field(
                        'عنوان ویدئو',
                        input('title', data.title, 'عنوان ویدئو')
                    )}
                `;

            case 'button':
                return `
                    ${field(
                        'متن دکمه',
                        input('text', data.text, 'مشاهده بیشتر')
                    )}

                    ${field(
                        'لینک دکمه',
                        input('url', data.url, 'https://example.com')
                    )}

                    ${field(
                        'استایل',
                        select('style', data.style, [
                            { value: 'primary', label: 'اصلی' },
                            { value: 'secondary', label: 'ثانویه' },
                            { value: 'success', label: 'سبز' }
                        ])
                    )}

                    ${field(
                        'تراز',
                        select('align', data.align, [
                            { value: 'right', label: 'راست' },
                            { value: 'center', label: 'وسط' },
                            { value: 'left', label: 'چپ' }
                        ])
                    )}
                `;

            case 'alert':
                return `
                    ${field(
                        'عنوان',
                        input('title', data.title, 'نکته مهم')
                    )}

                    ${field(
                        'متن',
                        textarea('text', data.text)
                    )}

                    ${field(
                        'نوع باکس',
                        select('type', data.type, [
                            { value: 'info', label: 'اطلاعات' },
                            { value: 'success', label: 'موفقیت' },
                            { value: 'warning', label: 'هشدار' },
                            { value: 'danger', label: 'خطر' }
                        ])
                    )}
                `;

            case 'divider':
                return field(
                    'نوع خط',
                    select('style', data.style, [
                        { value: 'solid', label: 'پیوسته' },
                        { value: 'dashed', label: 'خط‌چین' },
                        { value: 'dotted', label: 'نقطه‌چین' }
                    ])
                );

            case 'spacer':
                return field(
                    'ارتفاع فاصله',
                    input('height', data.height, '40')
                );

            case 'columns':
                return `
                    ${field(
                        'ستون راست',
                        textarea('left', data.left)
                    )}

                    ${field(
                        'ستون چپ',
                        textarea('right', data.right)
                    )}
                `;

            case 'code':
                return `
                    ${field(
                        'زبان کد',
                        input('language', data.language, 'php')
                    )}

                    ${field(
                        'کد',
                        textarea('code', data.code)
                    )}
                `;

            case 'poll':
                return `
                    ${field(
                        'سؤال نظرسنجی',
                        input('question', data.question, 'سؤال خود را وارد کنید...')
                    )}

                    ${field(
                        'گزینه‌ها',
                        textarea(
                            'options',
                            data.options.join('\n'),
                            'هر گزینه در یک خط'
                        )
                    )}
                `;

            case 'faq':
                return `
                    <div class="faq-items">
                        ${data.items.map((item, index) => `
                            <div class="faq-item">
                                ${field(
                                    `سؤال ${index + 1}`,
                                    input(`faq_question_${index}`, item.question)
                                )}

                                ${field(
                                    `پاسخ ${index + 1}`,
                                    textarea(`faq_answer_${index}`, item.answer)
                                )}
                            </div>
                        `).join('')}
                    </div>

                    <button
                        type="button"
                        class="add-faq-item"
                        data-id="${block.id}"
                    >
                        ＋ افزودن سؤال
                    </button>
                `;

            case 'gallery':
                return `
                    ${field(
                        'تعداد ستون',
                        select('columns', String(data.columns), [
                            { value: '2', label: 'دو ستون' },
                            { value: '3', label: 'سه ستون' },
                            { value: '4', label: 'چهار ستون' }
                        ])
                    )}

                    ${field(
                        'تصاویر',
                        textarea(
                            'images',
                            data.images.map(image => image.url).join('\n'),
                            'هر آدرس تصویر در یک خط'
                        )
                    )}
                `;

            default:
                return '<p>المان ناشناخته است.</p>';
        }
    }

    function renderPreview(block) {
        const data = block.data;

        switch (block.type) {
            case 'paragraph':
                return data.html || '<p>متن مقاله...</p>';

            case 'heading':
                return `<${data.level}>${escapeHtml(data.text)}</${data.level}>`;

            case 'image':
                return data.url
                    ? `<img src="${escapeHtml(data.url)}" alt="${escapeHtml(data.alt)}">`
                    : '<p>هنوز تصویری انتخاب نشده است.</p>';

            case 'quote':
                return `
                    <blockquote>
                        ${escapeHtml(data.text)}
                        ${data.author ? `<footer>— ${escapeHtml(data.author)}</footer>` : ''}
                    </blockquote>
                `;

            case 'video':
                return data.url
                    ? `<div>▶ ویدئو: ${escapeHtml(data.title || data.url)}</div>`
                    : '<p>آدرس ویدئو وارد نشده است.</p>';

            case 'button':
                return `
                    <div style="text-align:${escapeHtml(data.align)}">
                        <span class="builder-demo-button">
                            ${escapeHtml(data.text)}
                        </span>
                    </div>
                `;

            case 'alert':
                return `
                    <div class="builder-alert builder-alert-${escapeHtml(data.type)}">
                        <strong>${escapeHtml(data.title)}</strong>
                        <p>${escapeHtml(data.text)}</p>
                    </div>
                `;

            case 'divider':
                return `<hr style="border-style:${escapeHtml(data.style)}">`;

            case 'spacer':
                return `<div style="height:${parseInt(data.height || 40, 10)}px"></div>`;

            case 'columns':
                return `
                    <div class="builder-demo-columns">
                        <div>${data.left}</div>
                        <div>${data.right}</div>
                    </div>
                `;

            case 'code':
                return `
                    <pre><code>${escapeHtml(data.code)}</code></pre>
                `;

            case 'faq':
                return `
                    <div>
                        ${data.items.map(item => `
                            <details>
                                <summary>${escapeHtml(item.question)}</summary>
                                <div>${item.answer}</div>
                            </details>
                        `).join('')}
                    </div>
                `;

            case 'poll':
                return `
                    <div class="builder-poll">
                        <strong>${escapeHtml(data.question)}</strong>
                        <ul>
                            ${data.options.map(option => `
                                <li>○ ${escapeHtml(option)}</li>
                            `).join('')}
                        </ul>
                    </div>
                `;

            case 'gallery':
                return `
                    <div class="builder-gallery">
                        ${data.images.map(image => `
                            <img src="${escapeHtml(image.url)}" alt="">
                        `).join('')}
                    </div>
                `;

            default:
                return '';
        }
    }

    function render() {
        emptyState.style.display = blocks.length ? 'none' : 'block';

        container.innerHTML = '';

        blocks.forEach(block => {
            const element = document.createElement('article');

            element.className = 'builder-block';
            element.draggable = true;
            element.dataset.id = block.id;

            element.innerHTML = `
                <div class="block-toolbar">
                    <span class="block-type">
                        ${escapeHtml(labels[block.type] || block.type)}
                    </span>

                    <div class="block-actions">
                        <button type="button" data-action="up" title="انتقال به بالا">↑</button>
                        <button type="button" data-action="down" title="انتقال به پایین">↓</button>
                        <button type="button" data-action="duplicate" title="تکثیر">⧉</button>
                        <button type="button" data-action="delete" title="حذف">×</button>
                    </div>
                </div>

                <div class="block-content">
                    ${renderEditor(block)}

                    <div class="block-preview">
                        ${renderPreview(block)}
                    </div>
                </div>
            `;

            container.appendChild(element);
        });

        bindBlockEvents();
    }

    function updateFromElement(element) {
        const id = element.dataset.id;
        const block = blocks.find(item => item.id === id);

        if (!block) {
            return;
        }

        const fields = element.querySelectorAll('[data-field]');

        fields.forEach(fieldElement => {
            const name = fieldElement.dataset.field;
            let value = fieldElement.value;

            if (name === 'height') {
                value = parseInt(value || 40, 10);
            }

            if (name === 'options') {
                block.data.options = value
                    .split('\n')
                    .map(item => item.trim())
                    .filter(Boolean);

                return;
            }

            if (name === 'images') {
                block.data.images = value
                    .split('\n')
                    .map(url => ({
                        url: url.trim(),
                        alt: ''
                    }))
                    .filter(item => item.url !== '');

                return;
            }

            if (name.startsWith('faq_question_')) {
                const index = parseInt(
                    name.replace('faq_question_', ''),
                    10
                );

                if (block.data.items[index]) {
                    block.data.items[index].question = value;
                }

                return;
            }

            if (name.startsWith('faq_answer_')) {
                const index = parseInt(
                    name.replace('faq_answer_', ''),
                    10
                );

                if (block.data.items[index]) {
                    block.data.items[index].answer = value;
                }

                return;
            }

            block.data[name] = value;
        });

        const preview = element.querySelector('.block-preview');

        if (preview) {
            preview.innerHTML = renderPreview(block);
        }

        save();
    }

    function bindBlockEvents() {
        document.querySelectorAll('.builder-block').forEach(element => {
            element.addEventListener('input', () => {
                updateFromElement(element);
            });

            element.querySelectorAll('[data-action]').forEach(button => {
                button.addEventListener('click', () => {
                    const action = button.dataset.action;
                    const id = element.dataset.id;

                    if (action === 'delete') deleteBlock(id);
                    if (action === 'duplicate') duplicateBlock(id);
                    if (action === 'up') moveBlock(id, -1);
                    if (action === 'down') moveBlock(id, 1);
                });
            });

            element.addEventListener('dragstart', event => {
                event.dataTransfer.setData('text/plain', element.dataset.id);
                element.classList.add('dragging');
            });

            element.addEventListener('dragend', () => {
                element.classList.remove('dragging');
            });

            element.addEventListener('dragover', event => {
                event.preventDefault();
            });

            element.addEventListener('drop', event => {
                event.preventDefault();

                const draggedId = event.dataTransfer.getData('text/plain');
                const targetId = element.dataset.id;

                if (!draggedId || draggedId === targetId) {
                    return;
                }

                const draggedIndex = blocks.findIndex(
                    item => item.id === draggedId
                );

                const targetIndex = blocks.findIndex(
                    item => item.id === targetId
                );

                const draggedBlock = blocks.splice(draggedIndex, 1)[0];

                blocks.splice(targetIndex, 0, draggedBlock);

                render();
                save();
            });
        });
    }

    function save() {
        hiddenInput.value = JSON.stringify(blocks);
    }

    document.querySelectorAll('.element-item').forEach(button => {
        button.addEventListener('click', () => {
            addBlock(button.dataset.element);
        });
    });

    openElements.addEventListener('click', () => {
        elementsPanel.classList.add('open');
    });

    closeElements.addEventListener('click', () => {
        elementsPanel.classList.remove('open');
    });

    emptyAddElement.addEventListener('click', () => {
        elementsPanel.classList.add('open');
    });

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();

        document.querySelectorAll('.element-item').forEach(item => {
            item.style.display = item.textContent
                .toLowerCase()
                .includes(query)
                ? 'flex'
                : 'none';
        });
    });

    document.querySelector('#article-form').addEventListener('submit', () => {
        save();
    });

    render();
})();