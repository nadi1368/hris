/**
 * @var string seenRejectUrl
 * @var int manType
 * @var int maritalType
 */

window.createImageFromSrc = function (src) {
    const img = document.createElement('img')
    img.src = src
    return img
}

window.onAvatarLoad = function (src) {
    const img = createImageFromSrc(src)
    img.alt = 'avatar'
    img.style.width = '80px'
    img.style.height = '80px'
    img.style.objectFit = 'cover'
    img.style.objectPosition = 'center'
    img.classList.add('rounded-circle')
    const container = document.getElementById('avatar-container')
    container.replaceChild(img, container.children.item(0))
}

$(document).ready(function () {
    const form = $('#ajax-form-employee-update-profile')
    form.on('beforeValidate', function(e) {
        e.preventDefault();
    });
    form.on('afterValidate', function(e) {
        e.preventDefault();
        if (form.find('.is-invalid').length) {
            const tabHasError = form.find(".is-invalid").closest(".tab-pane");
            const tabLinkHasError = $('a[href="#' + tabHasError.attr("id") + '"');
            tabLinkHasError.tab('show');
        }
    });
    $('[data-file-browser]').on('click', function (e) {
        e.preventDefault()
        const fileInputKey = $(this).data('file-browser')
        const fileInput = fileInputKey && $('[data-file-input="' + fileInputKey + '"]')
        fileInput && fileInput.click()
    })

    $('[data-file-input]').on('change', function () {
        if (this.files && this.files.length) {
            const _this = $(this)
            const reader = new FileReader()
            reader.onload = function (e) {
                const onload = _this.data('onload')
                typeof window[onload] === 'function' && window[onload](e.target.result)
            }
            reader.readAsDataURL(this.files[0])
        }
    })

    $('#marital-select').on('change', function () {
        if (this.value?.toString() === maritalType?.toString()) {
            $('#children').removeClass('hide')
            $('#dateOfMarriage').removeClass('hide')
            $('#childCount').removeClass('hide')
        } else {
            $('#children').addClass('hide')
            $('#dateOfMarriage').addClass('hide')
            $('#childCount').addClass('hide')
        }
    })

    $('#sex-select').on('change', function () {
        $military = $('#military')
        if (this.value?.toString() === manType?.toString()) {
            $military.removeClass('hide')
        } else {
            $military.addClass('hide')
        }
    })

    function toggleEmptyBox(container) {
        const _container = $(container)[0]
        if (!_container) {
            return
        }
        if (!_container.children.length) {
            $(_container).html('<div class="empty-box">موردی یافت نشد</div>')
        } else {
            $(_container).find('.empty-box')?.remove()
        }
    }

    function activeJs(container) {
        const _container = $(container)
        if (!_container) {
            return
        }
        _container.find('.date-input').each((_, item) => {
            $(item).daterangepicker('', {
                locale: {
                    format: 'jYYYY/jMM/jDD'
                },
                drops: 'down',
                opens: 'right',
                jalaali: true,
                showDropdowns: true,
                language: 'fa',
                singleDatePicker: true,
                useTimestamp: true,
                timePicker: false,
                timePickerSeconds: true,
                timePicker24Hour: true
            })
        })
    }

    function resetDOM(item) {
        item.style.backgroundColor = 'transparent'
        $(item).find('small').each((_, hint) => {
            hint.remove()
        });
        ['uuid', 'deleted', 'added'].forEach((name) => {
            $(item).find(`input[name$="[${name}]"]`).each((_, hiddenInput) => {
                hiddenInput.value = ''
            })
        })
    }

    const childrenDynamicForm = $('.employee_children_dynamic_form')
    const experienceDynamicForm = $('.employee_experiences_dynamic_form')
    toggleEmptyBox('.employee-children')
    toggleEmptyBox('.employee-experiences');
    childrenDynamicForm.on('afterInsert', (_, item) => {
        toggleEmptyBox('.employee-children')
        activeJs(item)
        resetDOM(item)
    });
    experienceDynamicForm.on('afterInsert', (_, item) => {
        toggleEmptyBox('.employee-experiences')
        activeJs(item)
        resetDOM(item)
    });
    childrenDynamicForm.on('afterDelete', () => toggleEmptyBox('.employee-children'));
    experienceDynamicForm.on('afterDelete', () => toggleEmptyBox('.employee-experiences'));

    $('#military-checkbox').on('change', function () {
        if (this.checked) {
            $('#military-description').removeClass('hide')
            $('#military-doc').addClass('hide')
        } else {
            $('#military-description').addClass('hide')
            $('#military-doc').removeClass('hide')
        }
    })

    $(document).on('click', '#dismissRejectUpdate', function () {
        $(this).closest('.col-12').remove()
        $.ajax({
            url: seenRejectUrl,
            type: 'post'
        })
    })

    $('[data-gallery="show"]').on('click', function (e) {
        e.preventDefault();
        const items = [];
        const options = {
            index: $(this).data('gallery-index'),
            title: false,
            i18n: {
                minimize:'کوچک کردن',
                maximize:'بزرگ کردن',
                close:'بستن',
                zoomIn:'بزرگنمایی بشتر (+)',
                zoomOut:'بزرگنمایی کمتر (-)',
                prev:'قبل (←)',
                next:'بعد (→)',
                fullscreen:'تمام صفحه',
                actualSize:'سایز اصلی (Ctrl+Alt+0)',
                rotateLeft:'چرخاند به چپ (Ctrl+,)',
                rotateRight:'چرخاندن به راست (Ctrl+.)'
            },

        }

        $('[data-gallery="show"]').each(function () {
            let src = $(this).attr('href');
            items.push({
                src: src
            });
        })

        new PhotoViewer(items, options)
    });
})
