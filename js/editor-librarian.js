(function(wp) {
    const { registerPlugin } = wp.plugins;
    const { select, dispatch } = wp.data;
    const { useState } = wp.element;
    const { Modal, Button, TextareaControl, Icon, Popover, ToolbarGroup, ToolbarButton } = wp.components;
    const { RichTextToolbarButton, BlockControls } = wp.blockEditor;
    const { registerFormatType, insertObject } = wp.richText;
    const el = wp.element.createElement;

    // 1. AI Text Analysis Component
    // Using el() instead of JSX to ensure compatibility without a build step
    // Explicit fill color to ensure visibility in both light/dark editor modes
    const aiIcon = el('svg', { width: 20, height: 20, viewBox: "0 0 24 24", fill: "currentColor", style: { color: 'inherit' } },
        el('path', { d: "M19 5h-2V3h-2v2h-2v2h2v2h2V7h2v2h2V7h-2V5zm-2 14h-2v2h-2v-2h-2v-2h2v-2h2v2h2v2h2v2h-2v-2zM7 19h2v2h2v-2h2v-2h-2v-2H9v2H7v2H5v2h2v-2zM5 5h2v2h2V5h2V3H9v2H7V3H5v2H3v2h2V5z" }),
        el('circle', { cx: 12, cy: 12, r: 3 })
    );

    const emojiIcon = el('svg', { width: 20, height: 20, viewBox: "0 0 24 24", fill: "currentColor", style: { color: 'inherit' } },
        el('path', { d: "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5s.67 1.5 1.5 1.5zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" })
    );

    const LibrarianEditorPlugin = () => {
        const [isOpen, setIsOpen] = useState(false);
        const [originalText, setOriginalText] = useState('');
        const [improvedText, setImprovedText] = useState('');
        const [isLoading, setIsLoading] = useState(false);
        const [error, setError] = useState('');
        const [history, setHistory] = useState([]);

        const analyzeText = () => {
            const selectedBlock = select('core/block-editor').getSelectedBlock();
            if (!selectedBlock) return;

            const content = selectedBlock.attributes.content || selectedBlock.attributes.value || '';
            if (!content) {
                alert('Сначала введите текст в блок, чтобы я могла его проанализировать.');
                return;
            }

            setOriginalText(content);
            setIsOpen(true);
            setIsLoading(true);
            setError('');

            const requestData = {
                action: 'city_library_ai_chat',
                nonce: cityLibraryEditorAI.nonce,
                message: content,
                history: JSON.stringify(history.slice(-200)) // 100 requests limit (2 messages each)
            };

            if (history.length === 0) {
                requestData.message = `Проанализируй и улучши этот текст для библиотеки: "${content}"`;
            }

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: requestData,
                success: (response) => {
                    if (response.success) {
                        const reply = response.data.reply;
                        setImprovedText(reply);
                        setHistory(prev => [...prev,
                            { role: 'user', content: requestData.message },
                            { role: 'assistant', content: reply }
                        ].slice(-200));
                    } else {
                        setError(response.data.reply || 'Библиотекарь сейчас занят.');
                    }
                },
                error: () => setError('Ошибка связи с сервером.'),
                complete: () => setIsLoading(false)
            });
        };

        const replaceContent = () => {
            const selectedBlock = select('core/block-editor').getSelectedBlock();
            if (selectedBlock) {
                const attrName = selectedBlock.attributes.content !== undefined ? 'content' : 'value';
                dispatch('core/block-editor').updateBlockAttributes(selectedBlock.clientId, {
                    [attrName]: improvedText
                });
                setIsOpen(false);
            }
        };

        const selectedBlockType = select('core/block-editor').getSelectedBlock()?.name;
        const isTextBlock = selectedBlockType && ['core/paragraph', 'core/heading', 'core/list', 'core/quote'].includes(selectedBlockType);

        if (!isTextBlock) return null;

        return el(wp.element.Fragment, {},
            el(BlockControls, {},
                el(ToolbarGroup, {},
                    el(ToolbarButton, {
                        icon: aiIcon,
                        label: "Улучшить текст (ИИ)",
                        onClick: analyzeText,
                        className: "ai-librarian-toolbar-btn"
                    })
                )
            ),
            isOpen && el(Modal, {
                title: "✨ Анализ текста Виртуальным библиотекарем",
                onRequestClose: () => setIsOpen(false),
                className: "ai-editor-modal"
            },
                el('div', { style: { padding: '20px', minWidth: '450px' } },
                    isLoading ? el('div', { style: { textAlign: 'center', padding: '50px' } },
                        el(Icon, { icon: "update", className: "animate-spin", style: { fontSize: '40px', color: '#0b7930' } }),
                        el('p', { style: { marginTop: '15px', fontWeight: 'bold' } }, "Библиотекарь изучает ваш текст...")
                    ) : error ? el('div', { style: { padding: '20px', textAlign: 'center' } },
                        el('p', { style: { color: '#d32f2f' } }, error),
                        el(Button, { isSecondary: true, onClick: () => setIsOpen(false) }, "Закрыть")
                    ) : el(wp.element.Fragment, {},
                        el('div', { style: { marginBottom: '20px' } },
                            el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold', fontSize: '12px', textTransform: 'uppercase', color: '#64748b' } }, "Ваш оригинал:"),
                            el('div', { style: { background: '#f8fafc', padding: '15px', borderRadius: '12px', fontSize: '14px', border: '1px solid #e2e8f0', color: '#475569', fontStyle: 'italic' } }, originalText)
                        ),
                        el('div', { style: { marginBottom: '25px' } },
                            el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold', fontSize: '12px', textTransform: 'uppercase', color: '#0b7930' } }, "Версия библиотекаря:"),
                            el(TextareaControl, {
                                value: improvedText,
                                onChange: (val) => setImprovedText(val),
                                rows: 10,
                                style: { borderRadius: '12px', borderColor: '#0b7930', padding: '12px' }
                            }),
                            el('p', { style: { fontSize: '11px', color: '#94a3b8', marginTop: '5px' } }, "Вы можете отредактировать предложенный текст перед заменой.")
                        ),
                        el('div', { style: { display: 'flex', gap: '12px', justifyContent: 'flex-end', borderTop: '1px solid #f1f5f9', paddingTop: '20px' } },
                            el(Button, { isTertiary: true, onClick: () => setIsOpen(false) }, "Оставить как есть"),
                            el(Button, {
                                isPrimary: true,
                                onClick: replaceContent,
                                style: { background: '#0b7930', borderColor: '#0b7930', borderRadius: '8px', fontWeight: 'bold' }
                            }, "Применить изменения")
                        )
                    )
                )
            )
        );
    };

    registerPlugin('city-library-librarian-editor', {
        render: LibrarianEditorPlugin,
    });

    const EmojiPicker = ( props ) => {
        const [ isVisible, setIsVisible ] = useState( false );
        const emojis = ['📚', '📖', '📗', '📘', '📙', '📓', '📔', '📒', '📕', '✍️', '📝', '💡', '🏛️', '🎓', '✨', '📅', '📍', '📞', '👋', '😊', '🎭', '🎨', '🔍', '📌', '🤝'];

        return el(wp.element.Fragment, {},
            el(RichTextToolbarButton, {
                icon: emojiIcon,
                title: "Вставить эмодзи",
                onClick: () => setIsVisible( ! isVisible )
            }),
            isVisible && el(Popover, {
                onClose: () => setIsVisible( false ),
                position: "bottom center"
            },
                el('div', { style: { padding: '12px', display: 'grid', gridTemplateColumns: 'repeat(5, 1fr)', gap: '8px', background: '#fff', borderRadius: '8px' } },
                    emojis.map(emoji => el(Button, {
                        key: emoji,
                        isTertiary: true,
                        onClick: () => {
                            props.onChange(insertObject(props.value, {
                                type: 'city-library/emoji',
                                attributes: { content: emoji }
                            }));
                            setIsVisible(false);
                        },
                        style: { fontSize: '24px', padding: '8px', lineHeight: '1' }
                    }, emoji))
                )
            )
        );
    };

    registerFormatType('city-library/emoji', {
        title: 'Emoji',
        tagName: 'span',
        className: 'emoji-span',
        edit: EmojiPicker,
    });

})(window.wp);
