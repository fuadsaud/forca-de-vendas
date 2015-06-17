(function($) {
    var app = angular.module("forcaDeVendas", ['ngRoute', 'ngResource', 'ui.bootstrap', 'flow', 'LocalStorageModule', 'chart.js']);

    window.currentEditableElement = null;

    app.config(function($routeProvider, localStorageServiceProvider) {
        localStorageServiceProvider.setPrefix('forca-de-vendas')
            .setNotify(true, true)

        $routeProvider
            .when('/basket', {
                controller: 'BasketController',
                templateUrl: 'basket.html',
            })
            .when('/adm/products', {
                controller: 'AdmProductsController',
                action: 'list',
                templateUrl: 'admproducts.html',
            })
            .when('/adm/clients/list', {
                controller: 'ClientsController',
                action: 'list',
                templateUrl: 'clients.html',
            })
            .when('/adm/clients/add', {
                controller: 'ClientsController',
                action: 'add',
                templateUrl: 'clients.html',
            })
            .when('/adm/clients/edit/:id', {
                controller: 'ClientsController',
                action: 'edit',
                templateUrl: 'clients.html',
            })
            .when('/adm/users', {
                controller: 'UsersController',
                action: 'list',
                templateUrl: 'users.html',
            })
            .when('/adm/categories', {
                controller: 'CategoriesController',
                templateUrl: 'admcategories.html',
            })
            .when('/adm/payments', {
                controller: 'PaymentsController',
                templateUrl: 'admpayments.html',
            })
            .when('/adm/orders', {
                controller: 'OrdersController',
                templateUrl: 'admorders.html',
            })
            .when('/products', {
                controller: 'ProductsController',
                templateUrl: 'products.html',
            })
            .when('/users/:id/welcome/:hash', {
                controller: 'UsersController',
                action: 'welcome',
                templateUrl: 'users.html',
            })
            .otherwise({
                controller: 'MainController',
                templateUrl: 'main.html',
            });

    })
    .directive('ngContentEditable', ['$compile',
        function($compile) {
            return {
                priority: -1,
                restrict: 'A',
                link: function(scope, element, attr) {
                    var type = attr.ngContentEditable;
                    $(element).css({display: 'inline-block', width: '100%'});

                    var width = $(element).parent().width() + 80;

                    // bind click to element
                    element.bind('click', function() {
                        if (window.currentEditableElement) {
                            window.currentEditableElement.reset();
                        }
                        window.currentEditableElement = new ContentEditable(scope, element, type, width, $compile);
                    });
                }
            }
        }
    ])
    .directive('ngErrorMessage', function($tooltip){
        return {
            priority: 10,
            restrict: 'A',
            link: function($scope, element, attr) {
                $scope.$watch(attr.ngErrorMessage, function(current, old){
                    if (current) {
                        element.parent().addClass('has-error').addClass('has-feedback');
                        element.parent().append('<span class="glyphicon glyphicon-remove form-control-feedback" data-error-feedback aria-hidden="true"></span>')
                        element.attr('data-placement', 'bottom');
                        element.attr('data-content', $.map(current, function(value, index) {return value;}).join(".\n"))
                        element.popover({trigger: 'hover'});
                    } else {
                        element.popover('destroy')
                        element.parent().find('[data-error-feedback]').remove();
                        element.parent().removeClass('has-error').removeClass('has-feedback')
                    }
                })
            }
        }
    })
    .directive('equals', function() {
        return {
            require: 'ngModel',
            link: function(scope, elem, attr, ngModel) {
                scope.$watch(attr.equals, function(newValue, old) {
                    if (newValue != undefined) {
                        ngModel.$setValidity('equals', newValue == elem.val());
                    }
                })
                ngModel.$parsers.unshift(function(value){
                    var valid = scope.$eval(attr.equals) == value;
                    ngModel.$setValidity('equals', valid);
                    return value;
                })

                ngModel.$formatters.unshift(function(value){
                    var valid = scope.$eval(attr.equals) == value;
                    ngModel.$setValidity('equals', valid);
                    return value;
                })
            }
        }
    })
    .filter('splitMoney', function() {
        return function(input, index) {
            input = "" + input
            var info = input.split('.')
            var result = "00";
            if (index < info.length) {
                result = info[index];
            }
            if (result.length == 1) {
                result += "0";
            }
            if (index == 1) {
                result = result[0]+result[1];
            }
            return result;
        }
    })
    .factory('User', ['$resource', function($resource) {
        return $resource(BASEURL+'api/users/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'} }
        });
    }])
    .factory('UserPassword', ['$resource', function($resource) {
        return $resource(BASEURL+'api-change-password/:id/:hash', {id: '@_id', hash: '@_hash'}, {
            update: {method: 'PUT', params: {id: '@id', hash: '@hash'} }
        });
    }])
    .factory('Client', ['$resource', function($resource) {
        return $resource(BASEURL+'api/clients/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'} }
        });
    }])
    .factory('Group', ['$resource', function($resource) {
        return $resource(BASEURL+'api/groups/:id');
    }])
    .factory('Order', ['$resource', function($resource) {
        return $resource(BASEURL+'api/orders/:id');
    }])
    .factory('Product', ['$resource', function($resource) {
        return $resource(BASEURL+'api/products/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'}}
        });
    }])
    .factory('Payment', ['$resource', function($resource) {
        return $resource(BASEURL+'api/payments/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'}}
        });
    }])
    .factory('Category', ['$resource', function($resource) {
        return $resource(BASEURL+'api/categories/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'}}
        });
    }])
    .controller("MainController", function($scope, localStorageService, Product, Client) {
        $scope.BasketObject = new Basket(localStorageService, $scope, Product, Client)
    })
    .controller("ProductsController", ["$scope", 'Product', 'Category', function($scope, Product, Category) {
        var productsCrud = new CRUD($scope, Product, 'products', 'product');
        var basket = $scope.$parent.BasketObject;

        productsCrud.afterGet = function(entry) {
            var p = basket.getProduct(entry.id)
            if (p) {
                entry.quantity = p.quantity;
                entry.in_basket = true;
            }
            return entry;
        }

        Category.get({'show_all': 1}).$promise.then(function(r) {
            $scope.categories = r.categories;
            productsCrud.getList();
        });

        $scope.$watch('products', function() {
            setTimeout(adjustProducts, 100);
        })

        $scope.buy = function(product, quantity) {
            closeCurrentMessages();
            if (basket.getProduct(product.id)) {
                basket.changeQuantity(product.id, quantity);
                addMessage('success', 'Alterado com sucesso!')
            } else {
                addMessage('success', 'Adicionado com sucesso!')
                basket.addProduct(product, quantity);
            }
            product.quantity = basket.getProduct(product.id).quantity;
            product.in_basket = true;
        }

        $scope.getCategories = function(ids) {
            if (ids && ids.length > 0) {
                result = [];
                $.each(ids, function() {
                    var id = this;
                    $.each($scope.categories, function() {
                        if (this.id == id) {
                            result.push(this.name);
                            return false;
                        }
                    })
                })
                return result;
            }
        }

        $scope.filterBy = function(category) {
            if (category) {
                productsCrud.getList(null, null, null, {'category_id': category.id})
                $scope.selected_category = category.id;
            } else {
                productsCrud.getList()
                $scope.selected_category = null;
            }
        }

        $scope.product_detail = null;

        $scope.change = function(product) {
            product.quantity = parseInt(product.quantity);
            if (isNaN(product.quantity) || product.quantity <= 0) {
                product.quantity = 1;
            }
        }

        $scope.showMore = function($event, product) {
            var target = $($event.target).closest('.product');
            var position = $(target).offset();
            $('.big-product').css({
                'width': $(target).width(),
            }).offset(position);

            $scope.product_detail = product;
            var width = $('.container').width();
            $('.big-product').animate({
                'width': width,
                'top': '10px',
                'left': (($(window).width()-width)/2)+'px',
            }, 1000)
        }

        $scope.showLess = function() {
            $scope.product_detail = null;
            $('.big-product').animate({
                'width': '0',
            })
        }


    }])
    .controller("AdmProductsController", ['$scope','Product', 'Category', function($scope, Product, Category) {
        var productsCrud = new CRUD($scope, Product, 'products', 'product');
        productsCrud.afterGet = function(product) {
            product.price = (""+product.price).replace('.',',');
            return product;
        }

        var beforeSaveUpdate = function(product) {
            var aux = angular.copy(product);
            aux.price = aux.price.replace(',','.');
            return aux;
        }
        productsCrud.beforeSave = beforeSaveUpdate;
        productsCrud.beforeUpdate = beforeSaveUpdate;

        Category.get({'show_all': 1}).$promise.then(function(r) {
            $scope.categories = r.categories;
            $scope.categories_options = {};
            $.each(r.categories, function() {
                $scope.categories_options[this.id] = this.name;
            });
            productsCrud.getList();
        })

        $scope.getCategories = function(ids) {
            if (ids && ids.length > 0) {
                result = [];
                $.each(ids, function() {
                    var id = this;
                    $.each($scope.categories, function() {
                        if (this.id == id) {
                            result.push(this.name);
                            return false;
                        }
                    })
                })
                return result;
            }
        }

        $scope.uploadFile = function(id, $flow)
        {
            $flow.opts.target = BASEURL+'api/products/'+id+'/image';
            $flow.upload();
            closeCurrentMessages();
            addMessage('success', 'Imagem inserida com sucesso!');
        }
    }])
    .controller("BasketController", function($scope, Client, Payment, Order) {
        var Basket = $scope.$parent.BasketObject;
        $scope.products = Basket.getProducts();

        $scope.change = function(product) {
            product.quantity = parseInt(product.quantity);
            if (isNaN(product.quantity) || product.quantity <= 0) {
                product.quantity = 1;
            }
            Basket.changeQuantity(product.id, product.quantity);
        }

        $scope.remove = function(product) {
            Basket.removeProduct(product.id);
            $scope.products = Basket.getProducts();
        }
        $scope.$parent.$watch('change_basket_products', function() {
            $scope.products = Basket.getProducts();
        })

        $scope.$parent.$watch('change_basket_client', function() {
            $scope.selected_client = Basket.getClient();
        })

        $scope.$watch('selected_client', function(value, old) {
            if (value) {
                Basket.setClient(value);
            }
        }, true)

        Client.get({'show_all': 1}, function(r) {
            $scope.clients = r.clients;
            $scope.selected_client = Basket.getClient();
        })

        $scope.selected = {
            payment: {},
            payment_form: {}
        }

        $scope.$watch('selected.payment_form', function(value, old) {
            if (value && !$.isEmptyObject(value)) {
                Basket.setPaymentForm(value);
            }
        }, true)


        Payment.get({'show_all': 1}, function(r) {
            $scope.payments = r.payments;
            var basket_form = Basket.getPaymentForm();
            if (basket_form) {
                var exists = false;
                $.each($scope.payments, function() {
                    var payment = this;
                    if (payment.id == basket_form.payment_id) {
                        $.each(payment.forms, function() {
                            if (this.id == basket_form.id) {
                                exists = true;
                                $scope.selected.payment_form = this;
                                $scope.selected.payment = payment;
                            }
                        })
                        return false;
                    }
                })
                if (!exists) {
                    Basket.setPaymentForm(null);
                }
            }
        })

        $scope.finalize = function() {
            var products = Basket.getProducts();
            var client = Basket.getClient();
            var paymentForm = Basket.getPaymentForm();
            var hasError = false;

            var order = {
                items: []
            }
            closeCurrentMessages();
            if (client) {
                order.client_id = client.id
                $.each(client.addresses, function() {
                    if (this.type == 'billing') {
                        order.charge_address_id = this.id;
                    } else {
                        order.deliver_address_id = this.id;
                    }
                });
            } else {
                addMessage('danger', 'Selecione o cliente!');
                hasError = true;
            }

            if (paymentForm) {
                order.payment_form_id = paymentForm.id;
            } else {
                addMessage('danger', 'Selecione a forma de pagamento!');
                hasError = true;
            }

            if (products.length > 0) {
                $.each(products, function() {
                    order.items.push({
                        product_id: this.id,
                        quantity: this.quantity,
                    })
                })
            } else {

                addMessage('danger', 'Selecione ao menos um produto!');
                hasError = true;
            }

            if (!hasError) {
                Order.save(
                    order,
                    function(r){
                        closeCurrentMessages();
                        addMessage('success', 'Pedido #' + r.id +' criado com sucesso! O andamento de pedidos será realizado pelo setor de vendas através da ferramenta de ERP!', true);
                        Basket.clean();
                    },
                    function(r){
                        closeCurrentMessages();
                        addMessage('danger', 'Ocorreu um erro ao realizar o pedido!', true);
                    }
                );
            }
        }

    })
    .controller("UsersController", ['$scope', '$route', '$routeParams', 'User', 'UserPassword', 'Group', function($scope, $route, $routeParams, User, UserPassword, Group) {
        var usersCrud = new CRUD($scope, User, 'users', 'user');
        var hash;

        var render = function() {
            var renderAction = $route.current.action;
            if (renderAction == 'list') {
                $scope.groups = [];
                $scope.groups_options = {};
                Group.get({}).$promise.then(function(r) {
                    $scope.groups = r.groups;
                    $.each(r.groups, function() {
                        $scope.groups_options[this.id] = this.name;
                    })
                    usersCrud.getList();
                })
            } else if (renderAction == 'welcome') {
                var id = $routeParams.id;
                hash = $routeParams.hash;
                UserPassword.get({id: id, hash: hash}).$promise.then(function(r) {
                    $scope.user = r.user;
                }, function(r) {
                    window.location.href = BASEURL;
                })
            }
            $scope.renderAction = renderAction;
        }

        $scope.set_password = function(user) {
            if (user.password) {
                var usersPasswordCrud = new CRUD($scope, UserPassword, 'users', 'user');
                var entry = angular.copy(user);
                entry.hash = hash;
                usersPasswordCrud.update(entry).$promise.then(function(r) {
                    window.location.href = BASEURL;
                }, function() {
                    closeCurrentMessages();
                    addMessage('danger', 'Senhas não conferem!');
                    $scope.user = user;
                });
            }
        }

        $scope.$on(
            "$routeChangeSuccess",
            function ($currentRoute, $previousRoute) {
                // Update the rendering.
                render();
            }
        );
    }])
    .controller("CategoriesController", ['$scope', 'Category', function($scope, Category) {
        var categoriesCrud = new CRUD($scope, Category, 'categories', 'category');
        categoriesCrud.getList();
    }])
    .controller("ClientsController", ['$scope', '$route', '$routeParams', '$location', 'Client', function($scope, $route, $routeParams, $location, Client) {
        var clientsCrud = new CRUD($scope, Client, 'clients', 'client');
        $scope.clients = [];

        clientsCrud.afterSave = function(entry) {
            $location.path('adm/clients/list');
            return entry;
        }
        clientsCrud.afterUpdate = function(entry) {
            $location.path('adm/clients/list');
            return entry;
        }

        $scope.new_clients = function() {
            $location.path('adm/clients/add');
        }


        $scope.$watch('client', function() {
            if ($scope.client.same_addresses) {
                $scope.client.addresses[1] = angular.copy($scope.client.addresses[0]);
                $scope.client.addresses[1].type = 'delivery';
            }
        }, true)

        var render = function() {
            var id = ($routeParams.id || "");
            var renderAction = $route.current.action;
            if (renderAction == 'list') {
                clientsCrud.getList();
            } else if (renderAction == 'add') {
                $scope.client = {
                    same_addresses: false,
                    addresses: {
                        0: {
                            type: 'billing',
                            country: 'Brasil',
                        },
                        1: {
                            type: 'delivery',
                            country: 'Brasil',
                        }
                    }
                }
            } else if (renderAction == 'edit') {
                clientsCrud.get(id).$promise.then(function(){}, function() {
                    $location.path('adm/clients/list');
                });
            }
            $scope.renderAction = renderAction;
        }

        $scope.$on(
            "$routeChangeSuccess",
            function ($currentRoute, $previousRoute) {
                // Update the rendering.
                render();
            }
        );
    }])
    .controller("PaymentsController", ['$scope', 'Payment', function($scope, Payment) {
        var paymentsCrud = new CRUD($scope, Payment, 'payments', 'payment');
        paymentsCrud.getList();

        $scope.add_form = function(payment) {
            if (!payment.forms) {
                payment.forms = [];
            }
            payment.forms.push({
                description: '',
            });
        }

        $scope.remove_form = function(payment, index) {
            payment.forms.splice(index, 1);
        }

        var beforeSaveUpdate = function(payment) {
            var aux = angular.copy(payment);
            $.each(aux.forms, function() {
                if (this.interest) {
                    this.interest = this.interest.replace(',','.');
                }
            })
            return aux;
        }
        paymentsCrud.beforeSave = beforeSaveUpdate;
        paymentsCrud.beforeUpdate = beforeSaveUpdate;

        paymentsCrud.afterGet = function(payment) {
            $.each(payment.forms, function() {
                var aux = (""+this.interest).split('.');
                if (aux.length == 1) {
                    if (aux[0] != "") {
                        aux.push("00");
                    }
                } else if (aux[1].length == 1) {
                    aux[1] += "0";
                }
                this.interest = aux.join(',');
            })
            return payment;
        }

        paymentsCrud.afterUpdate= function(payment) {
            closeCurrentMessages();
            addMessage('success', 'Alterado com sucesso!', true)
            return payment;
        }

        paymentsCrud.afterSave= function(payment) {
            closeCurrentMessages();
            addMessage('success', 'Criado com sucesso!', true)
            return payment;
        }

        $scope.new_payments = function() {
            $scope.payment = {};
            $scope.add_form($scope.payment);
        }
    }])
    .controller('OrdersController', function($scope, $http, Order) {
        var ordersCrud = new CRUD($scope, Order, 'orders', 'order');
        ordersCrud.afterGet = function(order) {
            total = 0;
            $.each(order.items, function() {
                total += this.price * this.quantity;
            });
            order.total_items = total.toFixed(2).replace('.', ',');
            if (order.payment_interest > 0) {
                order.total = (total * (1 + order.payment_interest/100)).toFixed(2).replace('.',',');
            } else {
                order.total = order.total_items;
            }
            order.date = dateFormat(order.date).split(' ')[0];
            return order;
        }
        ordersCrud.getList();

        $http.get(BASEURL+'api/orders/0/mensal').success(function(r) {
            $scope.labels = [];
            $scope.totals = [[]];
            $scope.counts = [[]];

            $.each(r, function() {
                $scope.labels.push(this.date);
                $scope.counts[0].push(this.count);
                $scope.totals[0].push(Number(parseFloat(this.total).toFixed(2)));
            })
        })

        $http.get(BASEURL+'api/orders/0/anual').success(function(r) {
            $scope.labels_anual = [];
            $scope.totals_anual = [[]];
            $scope.counts_anual = [[]];

            $.each(r, function() {
                $scope.labels_anual.push(this.date);
                $scope.counts_anual[0].push(this.count);
                $scope.totals_anual[0].push(Number(parseFloat(this.total).toFixed(2)));
            })

        })
    })
})(jQuery);
