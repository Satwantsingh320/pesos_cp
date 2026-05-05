// new SimpleLightbox ('.lightbox', {});

// flatpickr(".flatpicker", {
//     dateFormat: "d-m-Y",
//     locale: current_lang
// });

// flatpickr(".flatpicker-future", {
//     minDate: Date.now(),
//     dateFormat: "d-m-Y",
//     locale: current_lang
// });

// flatpickr(".flatpicker-time", {
//     enableTime: true,
//     noCalendar: true,
//     dateFormat: "h:i K",
//     // locale: current_lang
// });

// flatpickr(".flatpicker-datetime", {
//     enableTime: true,
//     dateFormat: "d-m-Y h:i K",
//     // locale: current_lang
// });

// flatpickr(".flatpicker-range", {
//     mode: "range",
//     dateFormat: "d-m-Y",
//     onChange: function(selectedDates, dateStr, instance) {
//         instance.element.value = dateStr.replace(' to ', ' - ');
//     },
// });

// $('.datepicker').datepicker({
//     format: 'dd-mm-yyyy',
// });

// $(".select2").select2({
//     templateResult: function(state) {
//         if (!state.id) { return state.text; }
//         return $('<span>' + state.text + '</span>');
//     },
//     templateSelection: function(state) {
//         if (!state.id) { return state.text; }
//         return $('<span>' + state.text + '</span>');
//     },
// });

// $(".select2_section").select2({
//     closeOnSelect : false,
//     templateResult: function(state) {
//         if (!state.id) { return state.text; }
//         return $('<span>' + state.text + '</span>');
//     },
//     templateSelection: function(state) {
//         if (!state.id) { return state.text; }
//         return $('<span>' + state.text + '</span>');
//     },
// });

// $(".select2_tag").select2({
//     tags: true
// });

$('body').on('keypress', '.__numeric', function (e) {
    let KeyID = (window.event) ? event.keyCode : e.which;
    if ((KeyID >= 65 && KeyID <= 90) || (KeyID >= 97 && KeyID <= 99) || (KeyID >= 100 && KeyID <= 122) || (KeyID >= 33 && KeyID <= 47) || (KeyID >= 58 && KeyID <= 64) || (KeyID >= 91 && KeyID <= 95) || (KeyID >= 123 && KeyID <= 126)) {
        return false;
    }
});

let pageContainer = $('#pagination');
let url = pageContainer.data('url');
let total_record = 0;
let sortEntity = '';
let sortOrder = '';
let keyword = '';
let perPage = $('#perPage').val();
let token = $('meta[name="_token"]').attr('content');
let debounceTimeout;
let ajaxReq = null;
let formData = $('#form-search').serialize();
keyword = $('input[name=keyword]').val();

if (url != undefined) {
    pagination();
}

$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    url = $(this).attr('href');
    pagination();
    window.history.pushState('', '', url);
});

$('body').on('click', '#pagination th a', function (e) {
    e.preventDefault();
    sortEntity = $(this).attr('data-sortEntity');
    sortOrder = $(this).attr('data-sortOrder');
    pagination(true);
    window.history.pushState('', '', url);
});

$('body').on('click', '#pagination .dtr-title a', function (e) {
    e.preventDefault();
    sortEntity = $(this).attr('data-sortEntity');
    sortOrder = $(this).attr('data-sortOrder');
    pagination(true);
    window.history.pushState('', '', url);
});


$('body').on('change', '#perPage', function () {
    perPage = $(this).val();
    url = pageContainer.data('url');
    // window.history.pushState('', '', url);
    // pagination();
});

$('body').on('change keyup', 'input, textarea, select', function () {
    $(this).parent().find('.error').slideUp('slow');
});

$('body').on('keyup', 'input[name=keyword]', function () {
    // if (ajaxReq != null) ajaxReq.abort();
    clearTimeout(debounceTimeout);
    keyword = $(this).val();
    debounceTimeout = setTimeout(function () {
        pagination(true)
    }, 500);
});



$('body').on('click', '.filterTable .search-icon span', function () {
    keyword = $(this).val();
    pagination(true);
});

$('body').on('click', '.filterTable .__search', function () {
    keyword = $(this).val();
    pagination(true);
});

$('body').on('change', '#form-search input, #form-search select', function (e) {
    e.preventDefault();
    $('#form-search').find(".error").remove();
    /* formData = $('#form-search').serialize();
    url = pageContainer.data('url');
    window.history.pushState('', '', url);
    pagination(); */
});

$('#form-search').submit(function (e) {
    e.preventDefault();
    $('#form-search').find(".error").remove();
    formData = $(this).serialize();

    $('#form-filter').offcanvas('hide');
    $('.filter-icon').show();
    pagination(true);
});

$('#ajax-submit, .ajax-submit').submit(function (e) {
    e.preventDefault();

    try {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    } catch (e) {
        // print error
    }

    $(this).find(".error").remove();
    let form = $(this);

    $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            showLoader();
        },
        success: function (data) {
            toastr.options = {
                progressBar: true,
                onHidden: function () {
                    if (data.extra.redirect) {
                        window.location.href = data.extra.redirect;
                    }
                    else if (data.extra.reload) {
                        window.location.reload();
                    }
                }
            };

            if (data.success) {
                $(this).find("button[type='submit']").prop('disabled', true);
                hideLoader();

                if (data.extra.request_from && data.extra.request_from == 'contract_create') {
                    $('#contract').append(`<option value="${data.extra.id}">${data.extra.text}</option>`).val(data.extra.id).trigger('change');
                    $('#contractCreateModal').modal('hide');
                    form[0].reset();
                }


                if (data.message != '') {
                    toastr.success(data.message, success_msg, {
                        progressBar: true,
                        timeOut: "1000",
                    });
                }
                else {
                    window.location.href = data.extra.redirect;
                }
            }
            else {
                if (data.status == 206) {
                    hideLoader();
                    $.each(data.message, function (i, v) {
                        let error = '<div class="error">' + v + '</div>';
                        let split = i.split('.');
                        if (split[2]) {
                            var ind = split[0] + '[' + split[1] + ']' + '[' + split[2] + ']';
                            form.find("[name='" + ind + "']").parent().append(error);
                        } else if (split[1]) {
                            let ind = split[0] + '[' + split[1] + ']';
                            form.find("[name='" + ind + "']").parent().append(error);
                        } else {
                            form.find("[name='" + i + "']").parent().append(error);
                        }
                    });
                } else if (data.status == 207) {
                    hideLoader();

                    if (data.message != '') {
                        toastr.error(data.message);
                    }
                    else {
                        window.location.href = data.extra.redirect;
                    }
                }
            }
        },
        error: function (data) {
            console.log('An error occurred.');
        }
    });
});

$('body').on('click', '.__drop', function (e) {
    e.preventDefault();

    let params = new URLSearchParams(window.location.search);
    let paramsObj = Object.fromEntries(params.entries());
    paramsObj.page = paramsObj.page - 1;

    let queryParams = new URLSearchParams(paramsObj);
    let redirectUrl = window.location.origin + window.location.pathname + '?' + queryParams.toString();

    let msg = $(this).attr('data-confirm');
    let conf = confirm(msg);
    if (conf) {
        let option = {
            _token: token,
            _method: 'delete'
        };
        let route = $(this).attr('data-url');
        showLoader();
        $.ajax({
            type: 'post',
            url: route,
            data: option,
            success: function (data) {
                toastr.options = {
                    progressBar: true,
                    onHidden: function () {
                        if (data.extra.redirect) {
                            window.location.href = data.extra.redirect;
                        }
                        else if (data.extra.reload) {
                            window.location.reload();
                        }
                    }
                };

                if (data.success && data.status == 201) {
                    hideLoader();
                    toastr.success(data.message);

                    // toastr.success(data.message, success_msg, {
                    //     progressBar: true,
                    //     timeOut: "1000",
                    // });

                    if (total_record == 1) {
                        window.location.href = redirectUrl;
                    }

                    if (data.extra.reload == 1) {
                        window.location.reload();
                    }

                    pagination();
                    // window.location.reload();
                }
                else {
                    hideLoader();
                    toastr.error(data.message);
                }
            },
            error: function (data) {
                console.log(data);
                console.log('An error occurred.');
            }
        });
    }
});

// function debounce(func, delay = 500) {
//     return function (...args) {
//         clearTimeout(debounceTimeout);
//         debounceTimeout = setTimeout(() => {
//             func.apply(this, args);
//         }, delay);
//     };
// }


function ajaxFire(route, type, data = {}, callback) {
    data._token = token;
    $.ajax({
        type: type,
        url: route,
        data: data,
        beforeSend: function () {
            showLoader();
        },
        success: function (response) {
            hideLoader();

            if (response.status == 207) {
                alert(response.message);
                return false;
            }

            if (typeof callback === "function") {
                callback(response);
            }
        },
        error: function (data) {
            console.log('An error occurred.');
        }
    });
}

function pagination(fromSearch = false) {
    $('#form-search').find(".error").remove();

    let formData = $('#form-search').serialize();
    if (formData != '') {
        formData = formData + '&';
    }

    let option = formData + 'sortEntity=' + sortEntity +
        '&sortOrder=' + sortOrder +
        '&perPage=' + perPage +
        '&keyword=' + keyword +
        '&_token=' + token;

    if (fromSearch) {
        option = option + '&page=1';
    }

    ajaxReq = $.ajax({
        type: 'GET',
        url: url,
        data: option,
        beforeSend: function () {
            // pageContainer.html("<h5 class='text-center' style='height:50vh'>Loading...</h5>");
            showLoader();
        },
        success: function (data) {
            hideLoader();
            ajaxReq = null;
            if ((data.success == false) && (data.status == 206)) {
                $.each(data.message, function (i, v) {
                    let error = '<div class="error">' + v + '</div>';
                    $('#form-search').find("[name='" + i + "']").parent().append(error);
                });
            } else {
                pageContainer.html(data);
                if (pageContainer.find('.dataTable').length > 0 && !$.fn.DataTable.isDataTable('.dataTable')) {
                    pageContainer.find('.dataTable').DataTable({
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        // language: lang,
                        "fnDrawCallback": function (oSettings) {
                            if (oSettings.fnRecordsTotal() === 0) {
                            } else {
                                $("tfoot td").show();
                            }
                        }
                    });

                    total_record = pageContainer.find('.dataTable').find('tbody tr').length;
                    pageContainer.find('tfoot tr td').removeAttr('style');
                }
            }
        },
        error: function (data) {
            console.log('An error occurred.');
        }
    });
}

function showLoader() {
    document.getElementById("fullscreen-loader").classList.remove("d-none");
}

function hideLoader() {
    document.getElementById("fullscreen-loader").classList.add("d-none");
}

function slugify(string) {
    const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõőṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;'
    const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnoooooooooprrsssssttuuuuuuuuuwxyyzzz------'
    const p = new RegExp(a.split('').join('|'), 'g')

    return string.toString().toLowerCase()
        .replace(/\s+/g, '-') // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, '-and-') // Replace & with 'and'
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters
        .replace(/\-\-+/g, '-') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
}

function select2Change(target, route, data, cb = null) {
    $(target).html('');
    $(target).select2();

    data._token = token;
    $.ajax({
        type: 'POST',
        url: route,
        data: data,
        beforeSend: function () {
            // showLoader();
        },
        success: function (res) {
            if (res.success) {
                // hideLoader();

                $(target).html(res.options);
                $(target).select2();

                if (data.selected) {
                    $(target)
                        .val(data.selected)
                        .trigger('change.select2');
                }
                $(target).select2();
                if (typeof cb == 'function') {
                    cb(res);
                }
            }
        },
        error: function (data) {
            console.log('An error occurred.');
        }
    });
}

$('body').on('change', '.__country', function (e) {
    var value = $(this).val();
    if ($('.__state').length > 0) {
        if (value != '') {
            let route = $(this).attr('data-route');
            let title = $(this).attr('data-title');
            if (title == undefined) {
                title = '-Select-';
            }

            let data = {
                country_id: value,
                title: title,
            };
            select2Change('.__state', route, data);
        }
    }
});

$('body').on('change', '.__state', function (e) {
    var value = $(this).val();
    var countryValue = $('.__country').val();
    if ($('.__city').length > 0) {
        if (value != '') {
            let route = $(this).attr('data-route');
            let title = $(this).attr('data-title');
            if (title == undefined) {
                title = '-Select-';
            }

            let data = {
                country_id: countryValue,
                state_id: value,
                title: title,
            };
            select2Change('.__city', route, data);
        }
    }
});

$('body').on('click', '.__filter', function (e) {
    $('.__filter_box').slideToggle();
});
$('body').on('keypress', '.__numeric_decimal', function (e) {
    let charCode = e.which ? e.which : e.keyCode;
    let value = $(this).val();

    // Allow backspace
    if (charCode === 8) return true;

    // Allow only one decimal point
    if (charCode === 46) {
        return value.indexOf('.') === -1;
    }

    // Allow numbers 0–9
    if (charCode >= 48 && charCode <= 57) {
        return true;
    }

    return false;
});

$(document).ready(function () {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });

    // menu working code start
    let currentUrl = window.location.href;
    $("#scrollbar .nav-item").each(function () {
        var menuUrl = $(this).find('a').attr('href');
        if (currentUrl == menuUrl) {
            // $(this).find([`a[href="${currentUrl}"`]).addClass('active');
            $(this).find('a[href="' + currentUrl + '"]').addClass('active');
            $(this).closest('.menu-dropdown').addClass('show');
            $(this).closest('.menu-link').removeClass('collapsed');

            $(this).closest('.menu-dropdown').parent().closest('.menu-dropdown').addClass('show');
            $(this).closest('.menu-link').parent().closest('.menu-link').removeClass('collapsed');
        }
    });
    // menu working code end
});

$('body').on('change', '.__status', function () {

    let route = $(this).data('route');
    let $toggle = $(this);

    showLoader();

    $.ajax({
        type: 'POST',
        url: route,
        data: {
            _token: token
        },
        success: function (data) {
            hideLoader();

            if (data.success) {
                toastr.success(data.message);
            } else {
                toastr.error(data.message ?? 'Something went wrong');
                // revert toggle if failed
                $toggle.prop('checked', !$toggle.prop('checked'));
            }
        },
        error: function () {
            hideLoader();
            toastr.error('Server error');
            $toggle.prop('checked', !$toggle.prop('checked'));
        }
    });

});

$('body').on('change', '.__check_all', function () {
    if ($(this).is(':checked')) {
        $('.__check').prop('checked', true);
        $('.__check').parent().parent().addClass('bg-dark text-white');
    } else {
        $('.__check').prop('checked', false);
        $('.__check').parent().parent().removeClass('bg-dark text-white');
    }
});

$('body').on('change', '.__check', function () {
    if ($(this).is(':checked')) {
        $(this).parent().parent().addClass('bg-dark text-white');
    } else {
        $(this).parent().parent().removeClass('bg-dark text-white');
    }
});
$('body').on('click', '.__toggle_all', function () {

    if ($('.__check:checked').length <= 0) {
        $('.__modal_message').html(modal_message_lang);
        $('.__modal').modal('show');
        return false;
    }

    var ids = [];
    $(".__check:checked").each(function () {
        ids.push($(this).val());
    });

    var option = {
        _token: token,
        _method: 'post',
        ids: ids
    };
    var route = $(this).attr('data-route');
    showLoader();

    $.ajax({
        type: 'post',
        url: route,
        data: option,
        success: function (data) {
            hideLoader();
            if (data.success) {
                toastr.success(data.message);
                pagination();
            } else {
                toastr.error(data.message ?? 'Something went wrong');
                // revert toggle if failed
                $toggle.prop('checked', !$toggle.prop('checked'));
            }
        },
        error: function (data) {
            hideLoader();
            toastr.error('Server error');
            $toggle.prop('checked', !$toggle.prop('checked'));
        }
    });
});
//slug generated
let slugEdited = false;
function generateSlug(name) {
    return name.toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}
$(document).on('input', '#product_name', function () {
    if (!slugEdited) {
        // let slug = generateSlug($(this).val());
        // $('#product_slug').val(slug);
        $('#product_slug').val('');

    }
});
// $(document).on('input', '#product_slug', function () {
//     if (!slugEdited) {
//         generateSlug($(this).val());
//     }
// });
$('#regenSlug').on('click', function () {
    slugEdited = false;
    $('#product_slug').val(generateSlug($('#product_name').val()));
});


