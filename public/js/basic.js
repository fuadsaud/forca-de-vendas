$(document).ready(function() {
    'use strict';
    $.extend($.inputmask.defaults.definitions, {
        'H':  {
            validator: '[0-1][0-9]|2[0-3]',
            cardinality: 2,
            prevalidator: [
                { validator: '[0-2]', cardinality: 1 },
            ]
        },
        'M': {
            validator: '[0-5][0-9]',
            cardinality: 2,
            prevalidator: [
                { validator: '[0-5]', cardinality: 1 },
            ]
        },
        'd': {
            validator: '[0-2][0-9]|3[0-1]',
            cardinality: 2,
            prevalidator: [
                { validator: '[0-3]', cardinality: 1 },
            ]
        },
        'm': {
            validator: '0[0-9]|1[0-2]',
            cardinality: 2,
            prevalidator: [
                { validator: '[0-1]', cardinality: 1 },
            ]
        },
        'y': {
            validator: '(19|20)[0-9][0-9]',
            cardinality: 4,
            prevalidator: [
                { validator: '[12]', cardinality: 1 },
                { validator: '19|20', cardinality: 2 },
                { validator: '19|20\\d', cardinality: 3 },
            ]
        },
        'p': {
            validator: function(chrs, buffer, pos, strict, opts) {
                if (pos == 0) {
                    return /\d/.test(chrs);
                } else {
                    var aux = '';
                    for (var i=0; i<pos; i++) {
                        aux += ''+buffer.buffer[i];
                    }
                    aux += chrs;
                    if(pos == 1) {
                        return /\d\d/.test(aux);
                    } else {
                        return aux === '100';
                    }
                }
            },
            cardinality: 1,
        }
    });

    // behavior of date inputs
    $('body').on('focus','[data-date]', function() {
        $(this).inputmask('d/m/y', { placeholder: 'dd/mm/yyyy' });
        $(this).datepicker({
            dateFormat: 'dd/mm/yy',
            //changeMonth: true,
            //changeYear: true,
            onSelect: function (text, inst) {
                $(this).trigger('changeDate');
            },
            onClose: function (text, inst) {
                $(this).trigger('blurDate');
            }
        });
    });

    $('body').on('focus', '[data-currency]', function() {
        $(this).inputmask('}01,1{9,99', {numericInput: true});
    });

    // behavior of phone inputs
    $('body').on('focus', '[data-phone]', function() {
        $(this).inputmask('(99) 9999-9999?9');
    });

    $('body').on('focus', '[data-time]', function() {
        $(this).inputmask('H:M');
    });

    $('body').on('focus', '[data-percentage]', function() {
        $(this).inputmask('pp[p]', {autoUnmask: true});
    });
    $('body').on('focus', '[data-integer]', function() {
        $(this).inputmask('9{*}');
    });


    // behavior of select fields
    setTimeout(function() {
        $('[data-select]').select2();
    }, 100);
});


function refreshComponents() {
    setTimeout(function() {
        try {
            $('[data-select]').select2('destroy');
        } catch (e) {}
        $('[data-select]').select2();
        $('[data-select]').hide();
    }, 500);

}

function adjustProducts() {
    var cols = 4;
    var imgs = $('.product-img');
    var heights = [];

    $.each(imgs, function(index, img) {
        var pos = parseInt(index/cols);
        if (heights.length <= pos) {
            heights.push(0);
        }
        var aux = $(img).height();
        if (aux > heights[pos]) {
            heights[pos] = aux;
        }
    });

    $.each(imgs, function(index, img) {
        var pos = parseInt(index/cols);
        $(img).css('margin', ((heights[pos]-$(img).height())/2)+'px 0');
    });

    heights = []
    var titles = $('.product-title');
    $.each(titles, function(index, title) {
        var pos = parseInt(index/cols);
        if (heights.length <= pos) {
            heights.push(0);
        }
        var aux = $(title).height();
        if (aux > heights[pos]) {
            heights[pos] = aux;
        }
    });

    $.each(titles, function(index, title) {
        var pos = parseInt(index/cols);
        $(title).css('margin', ((heights[pos]-$(title).height())/2)+'px 0');
    });
}

function dateNormalize(string) {
    return string.replace(/(\d{2})\/(\d{2})\/(\d{4})/,'$3-$2-$1');
}

function dateFormat(string) {
    return string.replace(/(\d{4})-(\d{2})-(\d{2})/,'$3/$2/$1');
}

function addMessage(type, message, fixed, container) {
    var spanX = $('<span>').attr('aria-hiden', 'true').text('x');
    var spanClose = $('<span>').addClass('sr-only').text('Close');
    var button = $('<button>').addClass('close').addClass('message-close').attr('data-dismiss', 'alert');
    var div = $('<div>').addClass('alert').addClass('alert-dismissible').addClass('alert-'+type).attr('role', 'alert');

    $(button).append(spanX).append(spanClose);
    $(div).append(button);
    $(div).append(message);
    if (!container) {
        container = '#content';
    }

    if (fixed) {
        $(div).css({
            position: 'fixed',
            top: 0,
            width: $(container).width(),
            'z-index': 10,
        });
    }

    $(container).prepend(div);

}

function closeCurrentMessages(container) {
    if (!container) {
        container = '#content';
    }
    $('button.message-close', container).trigger('click');
}
