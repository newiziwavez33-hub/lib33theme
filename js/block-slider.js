(function(wp) {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
    var MediaPlaceholder = wp.blockEditor ? wp.blockEditor.MediaPlaceholder : wp.editor.MediaPlaceholder;
    var MediaUpload = wp.blockEditor ? wp.blockEditor.MediaUpload : wp.editor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor ? wp.blockEditor.MediaUploadCheck : wp.editor.MediaUploadCheck;
    var BlockControls = wp.blockEditor ? wp.blockEditor.BlockControls : wp.editor.BlockControls;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var Button = wp.components.Button;
    var ToolbarGroup = wp.components.ToolbarGroup;
    var ToolbarButton = wp.components.ToolbarButton || wp.components.IconButton;

    registerBlockType('city-library/slider', {
        title: 'Слайдер изображений',
        icon: 'images-alt2',
        category: 'media',
        attributes: {
            images: {
                type: 'array',
                default: []
            },
            ids: {
                type: 'string',
                default: ''
            },
            ratio: {
                type: 'string',
                default: 'auto'
            },
            effect: {
                type: 'string',
                default: 'fade'
            },
            objectFit: {
                type: 'string',
                default: 'contain'
            },
            autoplay: {
                type: 'boolean',
                default: true
            },
            style: {
                type: 'string',
                default: 'default'
            },
            showCaption: {
                type: 'boolean',
                default: true
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var onSelectImages = function(media) {
                var newImages = media.map(function(item) {
                    var displayUrl = (item.sizes && item.sizes.large) ? item.sizes.large.url : item.url;
                    var captionText = item.caption || item.alt || item.title || '';
                    return {
                        id: item.id,
                        url: displayUrl,
                        fullUrl: item.url,
                        caption: captionText,
                        alt: item.alt || ''
                    };
                });
                var newIds = newImages.map(function(item) { return item.id; }).join(',');

                setAttributes({
                    images: newImages,
                    ids: newIds
                });
            };

            var controls = el(
                InspectorControls,
                {},
                el(
                    PanelBody,
                    { title: 'Настройки слайдера', initialOpen: true },
                    el(SelectControl, {
                        label: 'Соотношение сторон',
                        value: attributes.ratio || 'auto',
                        options: [
                            { label: 'Адаптивный (Auto — по высоте фото)', value: 'auto' },
                            { label: 'Стандартный (16:9)', value: '16/9' },
                            { label: 'Классический (4:3)', value: '4/3' },
                            { label: 'Квадратный (1:1)', value: '1/1' },
                            { label: 'Портретный (3:4)', value: '3/4' },
                            { label: 'Широкий (21:9)', value: '21/9' }
                        ],
                        help: 'В режиме "Адаптивный" фотографии вписываются целиком без обрезки и искажений.',
                        onChange: function(value) { setAttributes({ ratio: value }); }
                    }),
                    el(SelectControl, {
                        label: 'Вписывание изображений',
                        value: attributes.objectFit || 'contain',
                        options: [
                            { label: 'Вмещение — без обрезки (Contain)', value: 'contain' },
                            { label: 'Заполнение — обрезка (Cover)', value: 'cover' }
                        ],
                        onChange: function(value) { setAttributes({ objectFit: value }); }
                    }),
                    el(ToggleControl, {
                        label: 'Показывать подписи к фото',
                        help: 'Отображает подпись (caption) или alt из медиабиблиотеки WordPress',
                        checked: attributes.showCaption !== false,
                        onChange: function(value) { setAttributes({ showCaption: value }); }
                    }),
                    el(SelectControl, {
                        label: 'Визуальный стиль',
                        value: attributes.style || 'default',
                        options: [
                            { label: 'По умолчанию (Скругления и рамка)', value: 'default' },
                            { label: 'Карточка с тенью', value: 'shadow-card' },
                            { label: 'Минимализм (Без фона)', value: 'minimal' },
                            { label: 'Брутализм (Черная рамка)', value: 'brutalism' }
                        ],
                        onChange: function(value) { setAttributes({ style: value }); }
                    }),
                    el(SelectControl, {
                        label: 'Эффект переключения',
                        value: attributes.effect || 'fade',
                        options: [
                            { label: 'Затухание (Fade)', value: 'fade' },
                            { label: 'Скольжение (Slide)', value: 'slide' },
                            { label: 'Куб (Cube)', value: 'cube' },
                            { label: 'Переворот (Flip)', value: 'flip' }
                        ],
                        onChange: function(value) { setAttributes({ effect: value }); }
                    }),
                    el(ToggleControl, {
                        label: 'Автовоспроизведение',
                        checked: attributes.autoplay !== false,
                        onChange: function(value) { setAttributes({ autoplay: value }); }
                    })
                ),
                el(
                    PanelBody,
                    { title: 'Управление галереей', initialOpen: false },
                    MediaUploadCheck ? el(
                        MediaUploadCheck,
                        {},
                        el(MediaUpload, {
                            onSelect: onSelectImages,
                            allowedTypes: ['image'],
                            multiple: true,
                            gallery: true,
                            value: attributes.images.map(function(img) { return img.id; }),
                            render: function(obj) {
                                return el(
                                    Button,
                                    {
                                        isSecondary: true,
                                        onClick: obj.open,
                                        style: { width: '100%', justifyContent: 'center' }
                                    },
                                    'Изменить состав изображений (' + attributes.images.length + ')'
                                );
                            }
                        })
                    ) : null
                )
            );

            var blockControls = (BlockControls && MediaUploadCheck) ? el(
                BlockControls,
                {},
                el(
                    ToolbarGroup || 'div',
                    {},
                    el(
                        MediaUploadCheck,
                        {},
                        el(MediaUpload, {
                            onSelect: onSelectImages,
                            allowedTypes: ['image'],
                            multiple: true,
                            gallery: true,
                            value: attributes.images.map(function(img) { return img.id; }),
                            render: function(obj) {
                                return el(ToolbarButton, {
                                    icon: 'edit',
                                    title: 'Редактировать галерею',
                                    onClick: obj.open
                                });
                            }
                        })
                    )
                )
            ) : null;

            if (attributes.images.length === 0) {
                return [
                    controls,
                    el(
                        MediaPlaceholder,
                        {
                            icon: 'images-alt2',
                            labels: { title: 'Изображения для слайдера' },
                            onSelect: onSelectImages,
                            accept: 'image/*',
                            multiple: true
                        }
                    )
                ];
            }

            var isAuto = (attributes.ratio === 'auto' || !attributes.ratio);
            var firstImage = attributes.images[0] || {};
            var firstCaption = firstImage.caption || '';

            // Preview render in Gutenberg
            return [
                controls,
                blockControls,
                el(
                    'div',
                    {
                        className: 'wp-block-city-library-slider',
                        style: {
                            position: 'relative',
                            border: '1px solid #e2e8f0',
                            borderRadius: '1rem',
                            overflow: 'hidden',
                            backgroundColor: attributes.style === 'minimal' ? 'transparent' : '#f8fafc',
                            display: 'flex',
                            flexDirection: 'column',
                            margin: '1.5rem 0'
                        }
                    },
                    el(
                        'div',
                        {
                            style: {
                                height: isAuto ? '380px' : 'auto',
                                aspectRatio: isAuto ? 'auto' : attributes.ratio,
                                maxHeight: '480px',
                                position: 'relative',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                background: '#f1f5f9',
                                backgroundImage: 'url(' + (firstImage.url || '') + ')',
                                backgroundSize: attributes.objectFit === 'cover' ? 'cover' : 'contain',
                                backgroundPosition: 'center',
                                backgroundRepeat: 'no-repeat'
                            }
                        },
                        el(
                            'div',
                            {
                                style: {
                                    position: 'absolute',
                                    bottom: '12px',
                                    right: '12px',
                                    background: 'rgba(15, 23, 42, 0.75)',
                                    color: 'white',
                                    padding: '4px 10px',
                                    borderRadius: '6px',
                                    fontSize: '12px',
                                    backdropFilter: 'blur(4px)',
                                    fontWeight: '500'
                                }
                            },
                            'Слайдов: ' + attributes.images.length + ' (' + (isAuto ? 'Адаптивный' : attributes.ratio) + ')'
                        )
                    ),
                    (attributes.showCaption !== false && firstCaption) ? el(
                        'div',
                        {
                            style: {
                                padding: '8px 16px',
                                fontSize: '13px',
                                fontStyle: 'italic',
                                color: '#64748b',
                                textAlign: 'center',
                                background: '#ffffff',
                                borderTop: '1px solid #e2e8f0'
                            }
                        },
                        firstCaption
                    ) : null
                )
            ];
        },

        save: function(props) {
            var attributes = props.attributes;
            if (!attributes.ids) return null;

            var ratioVal = attributes.ratio || 'auto';
            var effectVal = attributes.effect || 'fade';
            var fitVal = attributes.objectFit || 'contain';
            var styleVal = attributes.style || 'default';
            var autoplayVal = (attributes.autoplay !== false) ? 'true' : 'false';
            var captionVal = (attributes.showCaption !== false) ? 'true' : 'false';

            // Generate the shortcode explicitly to be rendered on the frontend
            var shortcode = '[city_library_slider ids="' + attributes.ids + '" ' +
                            'ratio="' + ratioVal + '" ' +
                            'effect="' + effectVal + '" ' +
                            'object_fit="' + fitVal + '" ' +
                            'style="' + styleVal + '" ' +
                            'show_caption="' + captionVal + '" ' +
                            'autoplay="' + autoplayVal + '"]';

            return el(wp.element.RawHTML, {}, shortcode);
        }
    });
})(window.wp);
