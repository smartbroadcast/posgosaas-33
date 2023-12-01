$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var session_key = $(location).attr("href").split('/').pop();

$(document).ready(function() {
    loadConfirm();

    // $('.dataTable').DataTable({
    //     language: dataTabelLang
    // });

    if ($(".select2").length > 0) {
        $($(".select2")).each(function(index, element) {
            var id = $(element).attr('id');
            var multipleCancelButton = new Choices(
                '#' + id, {
                    removeItemButton: true,
                }
            );
        });

    } else {

    }
});




function show_toastr(title, message, type) {
    var o, i;
    var icon = '';
    var cls = '';
    if (type == 'success') {
        icon = 'fas fa-check-circle';
        // cls = 'success';
        cls = 'primary';
    } else {
        icon = 'fas fa-times-circle';
        cls = 'danger';
    }
    console.log(type, cls);

    $.notify({ icon: icon, title: " " + title, message: message, url: "" }, {
        element: "body",
        type: cls,
        allow_dismiss: !0,
        placement: {
            from: 'top',
            align: 'right'
        },
        offset: { x: 15, y: 15 },
        spacing: 10,
        z_index: 1080,
        delay: 2500,
        timer: 2000,
        url_target: "_blank",
        mouse_over: !1,
        animate: { enter: o, exit: i },
        // danger
        template: '<div class="toast text-white bg-' + cls + ' fade show" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex">' +
            '<div class="toast-body"> ' + message + ' </div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" data-notify="dismiss" aria-label="Close"></button>' +
            '</div>' +
            '</div>'
            // template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
    });
}


function select2() {
    if ($(".select2").length > 0) {
        $($(".select2")).each(function(index, element) {
            var id = $(element).attr('id');
            var multipleCancelButton = new Choices(
                '#' + id, {
                    removeItemButton: true,
                }
            );
        });

    } else {

    }

}



function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.profile-image').attr('src', e.target.result);
            $('.profile-image').closest('div').find('button').removeClass('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function addCommas(num) {
    var number = parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    return ((site_currency_symbol_position == "pre") ? site_currency_symbol : '') + number + ((site_currency_symbol_position == "post") ? site_currency_symbol : '');
}

$(document).on('change', 'input[type="file"]', function(e) {
    var _URL = window.URL || window.webkitURL;
    var file, img;
    readURL(this);
    $('label[for="' + this.id + '"]').contents().first()[0].textContent = e.target.files[0].name;
});

$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"]', function(e) {
    e.preventDefault();

    var data = {};
    var title = $(this).data('title');
    var size = (($(this).data('size') == '') && (typeof $(this).data('size') === "undefined")) ? 'md' : $(this).data('size');
    var url = $(this).data('url');
    var align = $(this).data('align');

    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size + ' modal-dialog-' + align);

    if ($('#vc_name_hidden').length > 0) {
        data['vc_name'] = $('#vc_name_hidden').val();
    }

    $.ajax({
        url: url,
        data: data,
        success: function(data) {

            $('#commonModal .body').html(data);

            $("select option[value='']").prop('disabled', !$("select option[value='']").prop('disabled'));

            // if ($('[data-toggle="select"]').length > 0) {
            //     $('[data-toggle="select"]').select2({});
            // }

            if (($('#from').length > 0 && $('#to').length > 0)) {
                $("#from, #to").datepicker({ format: 'yyyy-mm-dd', startDate: new Date(), autoclose: true });
            }
            if ($('#date').length > 0) {
                $("#date").datepicker({ format: 'yyyy-mm-dd', autoclose: true });
            }

            if ($('#month').length > 0 && !$('#month').hasClass('edit-branch-target')) {
                $('#month').datepicker({
                    format: "MM-yyyy",
                    startView: "months",
                    minViewMode: "months",
                    autoclose: true
                });
            }

            if ($(".d_week").length > 0) {
                $($(".d_week")).each(function(index, element) {
                    var id = $(element).attr('id');

                    (function() {
                        const d_week = new Datepicker(document.querySelector('#' + id), {
                            buttonClass: 'btn',
                            format: 'yyyy-mm-dd',
                        });
                    })();

                });
            }

            if ($('#description').length > 0) {
                // CKEDITOR.replace('description');
            }

            $('#commonModal').modal('toggle');
            // $('#commonModal').modal({backdrop: 'static', keyboard: false});
        },
        error: function(data) {
            data = data.responseJSON;
            show_toastr('Error', data.message, 'error')
        }
    });
});

Array.prototype.remove = function() {
    var what, a = arguments,
        L = a.length,
        ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

// PLUS MINUS QUANTITY JS
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function(a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"),
            c.children().first().before('<input type="button" value="-" class="minus" />'),
            c.children().last().after('<input type="button" value="+" class="plus" />')
    })
}

String.prototype.getDecimals || (String.prototype.getDecimals = function() {
    var a = this,
        b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
    return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).ready(function() {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("updated_wc_div", function() {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("click", ".plus, .minus", function() {
    var a = jQuery(this).closest(".quantity").find('input[name="quantity"], input[name="quantity[]"]'),
        b = parseFloat(a.val()),
        c = parseFloat(a.attr("max")),
        d = parseFloat(a.attr("min")),
        e = a.attr("step");
    b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});

$(document).on('click', 'input[name="quantity"], input[name="quantity[]"]', function(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

$(document).on('keypress', 'input[name="phone_number"]', function(e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});

var Checklist = (function() {

    var $list = $('[data-toggle="checklist"]');

    function init($this) {
        var $checkboxes = $this.find('.checklist-entry input[type="checkbox"]');

        $checkboxes.each(function() {
            checkEntry($(this));
        });
    }

    function checkEntry($checkbox) {
        if ($checkbox.is(':checked')) {
            $checkbox.closest('.checklist-item').addClass('checklist-item-checked');
        } else {
            $checkbox.closest('.checklist-item').removeClass('checklist-item-checked');
        }
    }

    if ($list.length) {
        $list.each(function() {
            init($(this));
        });

        $list.find('input[type="checkbox"]').on('change', function() {
            checkEntry($(this));
        });
    }
})();


// Delete to open modal
(function($, window, i) {
    // Bootstrap 4 Modal
    $.fn.fireModal = function(options) {
            var options = $.extend({
                size: 'modal-md',
                center: false,
                animation: true,
                title: 'Modal Title',
                closeButton: true,
                header: true,
                bodyClass: '',
                footerClass: '',
                body: '',
                buttons: [],
                autoFocus: true,
                created: function() {},
                appended: function() {},
                onFormSubmit: function() {},
                modal: {}
            }, options);
            this.each(function() {

                if ($(this).attr('class').includes('trigger--fire-modal-')) {
                    return;
                }

                i++;
                var id = 'fire-modal-' + i,
                    trigger_class = 'trigger--' + id,
                    trigger_button = $('.' + trigger_class);
                $(this).addClass(trigger_class);
                // Get modal body
                let body = options.body;
                if (typeof body == 'object') {
                    if (body.length) {
                        let part = body;
                        body = body.removeAttr('id').clone().removeClass('modal-part');
                        part.remove();
                    } else {
                        body = '<div class="text-danger">Modal part element not found!</div>';
                    }
                }
                // Modal base template
                var modal_template = '   <div class="modal' + (options.animation == true ? ' fade' : '') + '" tabindex="-1" role="dialog" id="' + id + '">  ' +
                    '     <div class="modal-dialog ' + options.size + (options.center ? ' modal-dialog-centered' : '') + '" role="document">  ' +
                    '       <div class="modal-content">  ' +
                    ((options.header == true) ?
                        '         <div class="modal-header">  ' +
                        '           <h5 class="modal-title">' + options.title + '</h5>  ' +
                        ((options.closeButton == true) ?
                            '           <button type="button" class="close" data-dismiss="modal" aria-label="Close">  ' +
                            '             <span aria-hidden="true">&times;</span>  ' +
                            '           </button>  ' :
                            '') +
                        '         </div>  ' :
                        '') +
                    '         <div class="modal-body">  ' +
                    '         </div>  ' +
                    (options.buttons.length > 0 ?
                        '         <div class="modal-footer p-3">  ' +
                        '         </div>  ' :
                        '') +
                    '       </div>  ' +
                    '     </div>  ' +
                    '  </div>  ';
                // Convert modal to object
                var modal_template = $(modal_template);
                // Start creating buttons from 'buttons' option
                var this_button;
                options.buttons.forEach(function(item) {
                    // get option 'id'
                    let id = "id" in item ? item.id : '';
                    // Button template
                    this_button = '<button type="' + ("submit" in item && item.submit == true ? 'submit' : 'button') + '" class="' + item.class + '" id="' + id + '">' + item.text + '</button>';
                    // add click event to the button
                    this_button = $(this_button).off('click').on("click", function() {
                        // execute function from 'handler' option
                        item.handler.call(this, modal_template);
                    });
                    // append generated buttons to the modal footer
                    $(modal_template).find('.modal-footer').append(this_button);
                });
                // append a given body to the modal
                $(modal_template).find('.modal-body').append(body);
                // add additional body class
                if (options.bodyClass) $(modal_template).find('.modal-body').addClass(options.bodyClass);
                // add footer body class
                if (options.footerClass) $(modal_template).find('.modal-footer').addClass(options.footerClass);
                // execute 'created' callback
                options.created.call(this, modal_template, options);
                // modal form and submit form button
                let modal_form = $(modal_template).find('.modal-body form'),
                    form_submit_btn = modal_template.find('button[type=submit]');
                // append generated modal to the body
                $("body").append(modal_template);
                // execute 'appended' callback
                options.appended.call(this, $('#' + id), modal_form, options);
                // if modal contains form elements
                if (modal_form.length) {
                    // if `autoFocus` option is true
                    if (options.autoFocus) {
                        // when modal is shown
                        $(modal_template).on('shown.bs.modal', function() {
                            // if type of `autoFocus` option is `boolean`
                            if (typeof options.autoFocus == 'boolean')
                                modal_form.find('input:eq(0)').focus(); // the first input element will be focused
                            // if type of `autoFocus` option is `string` and `autoFocus` option is an HTML element
                            else if (typeof options.autoFocus == 'string' && modal_form.find(options.autoFocus).length)
                                modal_form.find(options.autoFocus).focus(); // find elements and focus on that
                        });
                    }
                    // form object
                    let form_object = {
                        startProgress: function() {
                            modal_template.addClass('modal-progress');
                        },
                        stopProgress: function() {
                            modal_template.removeClass('modal-progress');
                        }
                    };
                    // if form is not contains button element
                    if (!modal_form.find('button').length) $(modal_form).append('<button class="d-none" id="' + id + '-submit"></button>');
                    // add click event
                    form_submit_btn.click(function() {
                        modal_form.submit();
                    });
                    // add submit event
                    modal_form.submit(function(e) {
                        // start form progress
                        form_object.startProgress();
                        // execute `onFormSubmit` callback
                        options.onFormSubmit.call(this, modal_template, e, form_object);
                    });
                }
                $(document).on("click", '.' + trigger_class, function() {
                    $('#' + id).modal(options.modal);
                    return false;
                });
            });
        }
        // Bootstrap Modal Destroyer
    $.destroyModal = function(modal) {
        modal.modal('hide');
        modal.on('hidden.bs.modal', function() {});
    }
})(jQuery, this, 0);


$(document).on("click", '.bs-pass-para', function() {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: $(this).data('confirm'),
        text: $(this).data('text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {

            document.getElementById($(this).data('confirm-yes')).submit();

        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {}
    })
});



function loadConfirm() {

    // $('[data-confirm]').each(function () {
    //     var me = $(this),
    //         me_data = me.data('confirm');
    //     me_data = me_data.split('|');

    //     me.fireModal({
    //         title: me_data[0],
    //         body: me_data[1],
    //         buttons: [
    //             {
    //                 text: me.data('confirm-text-yes') || 'Yes',
    //                 class: 'btn btn-sm btn-danger rounded-pill',
    //                 handler: function () {
    //                     eval(me.data('confirm-yes'));
    //                 }
    //             },
    //             {
    //                 text: me.data('confirm-text-cancel') || 'Cancel',
    //                 class: 'btn btn-sm btn-secondary rounded-pill',
    //                 handler: function (modal) {
    //                     $.destroyModal(modal);
    //                     eval(me.data('confirm-no'));
    //                 }
    //             }
    //         ]
    //     })
    // });

}

$(function() {
    $(document).on("click", ".show_confirm", function() {
        var form = $(this).closest("form");
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "This action can not be undone. Do you want to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    });
});