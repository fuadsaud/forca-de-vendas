/*
 * CRUD component to manage angular controllers
 */
function CRUD($scp, md, pl_alias, sg_alias, ge_after_update, ge_after_save) {
    // current scope
    var $scope = $scp;
    // resource pbject
    var model = md;

    var get_entry_after_update = typeof ge_after_update != 'undefined' ? ge_after_update : false;
    var get_entry_after_save = typeof ge_after_save != 'undefined' ? ge_after_save : true;

    // alias in plural form
    var plural_alias = pl_alias;

    //alias in singular form
    var singular_alias = sg_alias;

    // class alias
    var klass = this;

    // current entry
    var currentEntry = null;

    this.setEntry = function(entry) {
        $scope[singular_alias] = entry;
    }

    this.setEntries = function(entries) {
        $scope[plural_alias] = entries;
    }

    this.getEntries = function() {
        return $scope[plural_alias];
    }

    // callback to parse request errors
    this.onError = function(response) {
        switch(response.status) {
            // unauthorized
            case 401:
                window.location.href = BASEURL;
                break;
            // when has an error on submit
            case 400:
                klass.setErrors(response.data.fields);
                break;

            // another errors
            default:
                //TODO show errors
                $.each(response.data.messages, function() {
                    alert(this.text);
                })
                break;
        }
    };

    // before save callback
    this.beforeSave = function(entry) {
        return entry;
    }

    // after save callback
    this.afterSave = function(entry) {
        return entry;
    }

    // before update callback
    this.beforeUpdate = function(entry) {
        return entry;
    }

    // after update callback
    this.afterUpdate = function(entry) {
        return entry;
    }

    // after get callback
    this.afterGet = function(entry) {
        return entry;
    }

    // function to show field errors
    this.setErrors = function (fields) {
        $.each(fields, function() {
            currentEntry.errors[this.name] = this.errors
        });
    }

    this.put_new_entry = function(entry) {
        klass.getEntries().unshift(entry);
    }

    // save an element
    this.save = function(entry) {
        var element = klass.beforeSave(entry)
        currentEntry = entry;
        currentEntry.errors = {}
        // save element
        return model.save(
            element,
            function(r) {
                klass.setEntry({});
                $scope._new = false;
                if (get_entry_after_save) {
                    model.get({ id: r.id }, function(res){
                        var entry = klass.afterSave(res[singular_alias])
                        // put entry on top of list
                        klass.put_new_entry(entry);
                    });
                } else {
                    var entry = klass.afterSave(element);
                    klass.put_new_entry(entry);
                }
            },
            function(r) { klass.onError(r) }
        );
    }


    // update an entry
    this.update = function(entry, callable) {
        currentEntry = entry;
        currentEntry.errors = {}
        var element = klass.beforeUpdate(entry)
        return model.update(
            element,
            function(r) {
                if (get_entry_after_update) {
                    if (klass.getEntries()) {
                        model.get({id: entry.id}, function(res) {
                            entry = klass.afterUpdate(res[singular_alias]);
                            klass.replace_entry(entry);
                        })
                    }
                } else {
                    entry = klass.afterUpdate(entry)
                    if (klass.getEntries()) {
                        klass.replace_entry(entry);
                    }
                }
                // was passed a callable function?
                if (typeof callable == 'function') {
                    callable(entry);
                }
            },
            function(r) { klass.onError(r)}
        );
    }

    this.activate = function(entry) {
        entry.active = 1;
        return klass.update(entry);
    }

    this.replace_entry = function(entry) {
        klass.getEntries().forEach(function(item, index) {
            if (item.id == entry.id) {
                // reload entry information
                klass.getEntries()[index] = entry;
                return;
            }
        });
    }

    // remove an element
    this.delete = function (id) {
        var defaultOptions = { remove: true };
        return model.delete(
            {id: id},
            function(r){
                klass.getEntries().forEach(function(item, index) {
                    if (item.id == id) {
                        if ($scope['show_inactive_'+plural_alias]) {
                            item.active = false;

                            klass.replace_entry(item);
                        } else {
                            // remove entry of list
                            klass.getEntries().splice(index, 1);
                        }
                    }
                });
            },
            function(r) { klass.onError(r) }
        );
    }

    // get an entry
    this.get = function(id, params) {
        var options = {id: id};
        if (params) {
            options = $.extend({}, params, options);
        }
        return model.get(
            options,
            function(res) {
                var entry = klass.afterGet(res[singular_alias]);
                klass.setEntry(entry);
            },
            function(res) { klass.onError(res) }
        );
    }

    // get elements list
    this.getList = function (page, sort, order, additionalParams) {
        //was passed a page?
        if (!page && $scope.page) {
            page = $scope.page;
        }

        var params = {
            page: page,
            sort: sort,
            order: order,
        }
        if (typeof additionalParams == 'object') {
            $.extend(params, additionalParams);
        }
        if ($scope["show_inactive_"+plural_alias]) {
            params.show_inactive = true;
        }

        return model.get(
            params,
            function(res) {
                var page = res['page'], pages = res['pages'], i, startPage;
                var entries = res[plural_alias];
                $.each(entries, function(index, item) {
                    entries[index] = klass.afterGet(item);
                })
                klass.setEntries(entries);
                $scope.page = page;
                $scope.totalPages = pages;
                $scope.pages = [];
                if (pages > 1) {
                    startPage = page - 2;
                    if (startPage <= 0) {
                        startPage = 1;
                    }
                    for (i=startPage; i<=startPage+5; i++) {
                        if (i > 0 && i <= pages) {
                            $scope.pages.push(i);
                        }
                    }
                }
            },
            function(r) { klass.onError(r) }
        );
    }

    // change list order
    this.order = function (column) {
        if ($scope.column == column) {
            $scope.direction = ($scope.direction == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $scope.direction = 'ASC';
        }
        $scope.column = column;
        klass.getList(null, $scope.column, $scope.direction);
    }

    // registration of scope functions
    $scope["save_" + singular_alias] = function(entry) {
        return klass.save(entry);
    }

    $scope["update_" + singular_alias] = function (entry, callable) {
        return klass.update(entry, callable);
    }

    $scope["delete_" + singular_alias] = function(id) {
        return klass.delete(id);
    }

    $scope["order_" + singular_alias] = function(column) {
        klass.order(column);
    }

    klass.setEntry(new model());

    $scope["go_to_" + plural_alias + "_page"] = function(page) {
        return klass.getList(page);
    }
    $scope["get_"+plural_alias+"_list"] = function() {
        return klass.getList();
    }

    $scope["activate_"+singular_alias] = function(entry) {
        return klass.activate(entry);
    }
    // end of registrations

}
