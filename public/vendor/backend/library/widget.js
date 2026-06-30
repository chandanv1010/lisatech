(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');
    var typingTimer;
    var doneTyingInterval = 300; // 1s

    HT.searchModel = () => {
        $(document).on('keyup', '.search-model', function (e) {
            e.preventDefault()
            let _this = $(this)
            if ($('input[type=radio]:checked').length === 0) {
                alert('Bạn chưa chọn Module');
                _this.val('')
                return false;
            }
            let keyword = _this.val()
            let option = {
                model: $('input[type=radio]:checked').val(),
                keyword: keyword
            }
            HT.sendAjax(option)

        })
    }

    HT.chooseModel = () => {
        $(document).on('change', '.input-radio', function () {
            let _this = $(this)
            let option = {
                model: _this.val(),
                keyword: $('.search-model').val()
            }
            $('.search-model-result').html('');
            if (option.keyword.length >= 2) {
                HT.sendAjax(option)
            }


        })
    }

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            $.ajax({
                url: 'ajax/dashboard/findModelObject',
                type: 'GET',
                data: option,
                dataType: 'json',
                beforeSend: function () {
                    $('.ajax-search-result').html('').hide();
                },
                success: function (res) {
                    // ✅ Lọc trước: chỉ giữ item có đủ languages + pivot
                    let validItems = res.filter(function (item) {
                        return item.languages
                            && Array.isArray(item.languages)
                            && item.languages.length > 0
                            && item.languages[0].pivot;
                    });

                    let html = HT.renderSearchResult(validItems);

                    if (html.length) {
                        $('.ajax-search-result').html(html).show();
                    } else {
                        $('.ajax-search-result').html('').hide();
                    }
                },
                error: function (xhr, status, err) {
                    console.error('Ajax error:', status, err);
                    $('.ajax-search-result').html('').hide();
                }
            });
        }, doneTyingInterval);
    }

    HT.renderSearchResult = (data) => {
        let html = '';

        if (!data || !data.length) return html;

        for (let i = 0; i < data.length; i++) {
            let item = data[i];

            // ✅ Guard: bỏ qua item thiếu languages/pivot
            let hasPivot = item.languages
                && Array.isArray(item.languages)
                && item.languages.length > 0
                && item.languages[0].pivot;

            if (!hasPivot) continue;

            let pivot = item.languages[0].pivot;
            let canonical = pivot.canonical || '';
            let name = pivot.name || 'Không có tên';
            let image = item.image || '';
            let id = item.id;

            // ✅ Dùng class check thay vì id để tránh timing bug
            let alreadySelected = $('.search-model-result')
                .find('[data-modelid="' + id + '"]')
                .length > 0;
            let flag = alreadySelected ? 1 : 0;
            let checkIcon = alreadySelected ? HT.setChecked() : '';

            html += `<button
                    class="ajax-search-item"
                    data-flag="${flag}"
                    data-canonical="${canonical}"
                    data-image="${image}"
                    data-name="${name}"
                    data-id="${id}"
                >
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span>${name}</span>
                <div class="auto-icon">${checkIcon}</div>
            </div>
        </button>`;
        }

        return html;
    }

    HT.setChecked = () => {
        return '<svg class="svg-next-icon button-selected-combobox svg-next-icon-size-12" width="12" height="12"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26"><path d="m.3,14c-0.2-0.2-0.3-0.5-0.3-0.7s0.1-0.5 0.3-0.7l1.4-1.4c0.4-0.4 1-0.4 1.4,0l.1,.1 5.5,5.9c0.2,0.2 0.5,0.2 0.7,0l13.4-13.9h0.1v-8.88178e-16c0.4-0.4 1-0.4 1.4,0l1.4,1.4c0.4,0.4 0.4,1 0,1.4l0,0-16,16.6c-0.2,0.2-0.4,0.3-0.7,0.3-0.3,0-0.5-0.1-0.7-0.3l-7.8-8.4-.2-.3z"></path></svg></svg>'
    }


    HT.unfocusSearchBox = () => {
        $(document).on('click', 'html', function (e) {
            if (!$(e.target).hasClass('search-model-result') && !$(e.target).hasClass('search-model')) {
                $('.ajax-search-result').html('')
            }
        })

        $(document).on('click', '.ajax-search-result', function (e) {
            e.stopPropagation();
        })
    }

    HT.addModel = () => {
        $(document).on('click', '.ajax-search-item', function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _this = $(this);

            // ✅ Luôn đọc flag từ attr, KHÔNG dùng .data() vì jQuery cache
            let flag = parseInt(_this.attr('data-flag')) || 0;
            let id = _this.attr('data-id');
            let name = _this.attr('data-name');
            let image = _this.attr('data-image');
            let canonical = _this.attr('data-canonical');

            let itemData = { id, name, image, canonical };

            console.log('Clicked | id:', id, '| flag:', flag, '| name:', name);

            if (flag === 0) {
                // ✅ Chưa chọn → thêm vào list
                _this.find('.auto-icon').html(HT.setChecked());
                _this.attr('data-flag', '1'); // ← set string '1' cho nhất quán
                $('.search-model-result').append(HT.modelTemplate(itemData));
            } else {
                // ✅ Đã chọn → bỏ chọn
                $('#model-' + id).remove();
                _this.find('.auto-icon').html('');
                _this.attr('data-flag', '0');
            }
        });
    }


    HT.modelTemplate = (data) => {
        let html = `<div class="search-result-item" id="model-${data.id}" data-modelid="${data.id}">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="uk-flex uk-flex-middle">
                    <span class="image img-cover"><img src="${data.image}" alt=""></span>
                    <span class="name">${data.name}</span>
                    <div class="hidden">
                        <input type="text" name="modelItem[id][]" value="${data.id}">
                        <input type="text" name="modelItem[name][]" value="${data.name}">
                        <input type="text" name="modelItem[image][]" value="${data.image}">
                    </div>
                </div>
                <div class="deleted">
                    <svg class="svg-next-icon svg-next-icon-size-12" width="12" height="12">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                            <path d="M18.263 16l10.07-10.07c.625-.625.625-1.636 0-2.26s-1.638-.627-2.263 0L16 13.737 5.933 3.667c-.626-.624-1.637-.624-2.262 0s-.624 1.64 0 2.264L13.74 16 3.67 26.07c-.626.625-.626 1.636 0 2.26.312.313.722.47 1.13.47s.82-.157 1.132-.47l10.07-10.068 10.068 10.07c.312.31.722.468 1.13.468s.82-.157 1.132-.47c.626-.625.626-1.636 0-2.26L18.262 16z">
                                
                            </path>
                        </svg>
                    </svg>
                </div>
            </div>
        </div>`
        return html
    }

    HT.removeModel = () => {
        $(document).on('click', '.deleted', function () {
            let _this = $(this)
            _this.parents('.search-result-item').remove()
        })
    }


    $(document).ready(function () {
        HT.searchModel()
        HT.chooseModel()
        HT.unfocusSearchBox()
        HT.addModel()
        HT.removeModel()
    });

})(jQuery);
