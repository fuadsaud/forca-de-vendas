var ContentEditable = function(sc, el, tp, w, comp) {
    var scope = sc;
    var element = el;
    this.element = element;
    var type = tp.split(':')[1];
    var alias = tp.split(':')[0];
    var width = w;
    var $compile = comp;

    var klass = this;
    var model = $(element).attr('ng-model')
    var value = scope.$eval(model);

    if (value == null) {
        value = "";
    }

    // value buffer
    $(element).data('value', value);

    $(element).hide();

    // input
    var input;
    switch (type) {
        case 'date':
            input = $('<input>').attr({'data-date': '', type: 'text'})
            break;
        case 'time':
            input = $('<input>').attr({'data-time': '', type: 'text'});
            type = 'text';
            break;
        case 'currency':
            input = $('<input>').attr({'data-currency': '', type: 'text'});
            break;
        case 'integer':
            input = $('<input>').attr({'data-integer': '', type: 'text'});
            break;
        case 'percentage':
            input = $('<input>').attr({'data-percentage': '', type: 'text'});
            break;
        case 'select':
            input = $('<select>').attr({'data-select': ''});
            if ($(element).data('multiple') != undefined) {
                $(input).attr('multiple', 'multiple');
            }
            var options = scope.$eval($(element).data('options'));
            $.each(options, function(key, v) {
                var option =$('<option>').val(key).text(v);
                $(input).append(option);
            })
            break;
        case 'select-group':
            input = $('<input>').attr('type', 'hidden');
            break;
        case 'text':
        default:
            input = $('<input>').attr('type','text');
    }

    $(input).addClass('form-control').val(value);

    // buttons
    var editButton = $('<button>')
                        .addClass('btn')
                        .addClass('btn-primary')
                        .append($('<i>').addClass('glyphicon glyphicon-ok'))
    var cancelButton = $('<button>')
                        .addClass('btn')
                        .addClass('btn-danger')
                        .append($('<i>').addClass('glyphicon glyphicon-remove'))


    // container of elements
    var container = $('<div>').append(
                        $('<div>')
                            .css({ 'max-width': width + 'px', 'min-width': '150px' })
                            .addClass('input-group input-group-sm')
                            .append(input)
                            .append(
                                $('<span>')
                                    .addClass('input-group-btn')
                                    .append(editButton)
                                    .append(cancelButton)
                            )
                    ).addClass('content-editable-container');

    // should show errors?
    if ($(element).data('errors')) {
        var error = $('<em>').addClass('clearfix')
                             .addClass('text-danger')
                             .attr({'ng-repeat':'error in '+$(element).data('errors')})
                             .text('{{error}}');
        container.append(error);

        // listener to errors messages
        scope.$watch(
            function($scope) {
                return $scope.$eval($(element).data('errors'));
            },
            function(value) {
                $compile(error)(scope);
            }
        );
    }

    // put container after of element
    $(element).after(container);

    // function to hide input
    var hideInput = function(entry) {
        if (entry) {
            var v = scope.$eval(model);
            $(element).data('value', v);
        }
        $(container).fadeOut(300, function() {
            $(container).remove();
            $(element).show();
        });
    }

    // function to reset changes and hide input
    this.reset = function() {
        hideInput(null);
        if ($(element).data('errors')) {
            scope.$eval($(element).data('errors') + ' = {}');
        }

        var aux = $(element).data('value');

        if (typeof aux !== 'undefined') {
            setValue($(element).data('value'));
        }
        scope.$apply();
    }

    // function to save entry
    var save = function() {
        scope["update_" + alias](scope[alias], hideInput);
    }

    // function to set value on scope
    var setValue = function(val) {
        if (type == 'percentage' || type == 'currency') {
            val = val.replace(/_/,'');
        }
        if (val instanceof Object) {
            scope.$eval(model + '= []');
            $.each(val, function() {
                scope.$eval(model + '.push("' + this + '")');
            })
        } else {
            scope.$eval(model + '= "' + val + '"');
        }
    }

    // value has change?
    var hasChanged = function(val) {
        return val == value;
    }

    // bind click to buttons
    editButton.bind('click', save);
    cancelButton.bind('click', klass.reset);

    // bind events to input
    if (type == 'date') {
        input.bind('blurDate', function() {
            if (hasChanged($(input).val())) {
                klass.reset();
            }
        });
        input.bind('changeDate', function() {
            setValue($(this).val());
            $(input).focus();
        })
    } else if (type == 'select-group') {
        var options = scope.$eval($(element).data('options'));
        $(input).select_group(options);
    } else if (type != 'select') {
        input.bind('blur', function() {
            if (hasChanged($(input).val())) {
                klass.reset();
            }
        });
    }

    var removeDatePicker = function() {
        $(input).datepicker('hide');
    }


    if (type == 'select' || type == 'select-group') {
        input.bind('change', function() {
            setValue($(input).val());
        });
    }
    // change value by keyboard
    input.bind('keyup', function(event) {
        switch(event.which) {
            case 13: // enter
                save();
                if (type == 'date') {
                    removeDatePicker();
                }
                break;
            case 27: // esc
                klass.reset();
                if (type == 'date') {
                    removeDatePicker();
                }
                break;
            default:
                setValue($(input).val());
        }
    });

    // input autofocus
    $(input).focus();

    scope.$apply();

    refreshComponents();
}
