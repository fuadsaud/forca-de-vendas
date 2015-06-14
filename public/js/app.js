(function($) {
    var app = angular.module("forcaDeVendas", ['ngRoute', 'ngResource', 'ui.bootstrap', 'flow']);

    window.currentEditableElement = null;

    app.config(function($routeProvider) {
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
            .when('/adm/users', {
                controller: 'UsersController',
                action: 'list',
                templateUrl: 'users.html',
            })
            .when('/adm/categories', {
                controller: 'CategoriesController',
                templateUrl: 'admcategories.html',
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
                        element.attr('data-content', current.join("\n"))
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
            return result;
        }
    })
    .factory('User', ['$resource', function($resource) {
        return $resource(BASEURL+'api/users/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'} }
        });
    }])
    .factory('Group', ['$resource', function($resource) {
        return $resource(BASEURL+'api/groups/:id');
    }])
    .factory('Product', ['$resource', function($resource) {
        return $resource(BASEURL+'api/products/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'}}
        });
    }])
    .factory('Category', ['$resource', function($resource) {
        return $resource(BASEURL+'api/categories/:id', {id: '@_id'}, {
            update: {method: 'PUT', params: {id: '@id'}}
        });
    }])
    .controller("MainController", function($scope) {
        $scope.test = 'oi';
    })
    .controller("ProductsController", ["$scope", 'Product', 'Category', function($scope, Product, Category) {
        var productsCrud = new CRUD($scope, Product, 'products', 'product');
        Category.get({'show_all': 1}).$promise.then(function(r) {
            $scope.categories = r.categories;
            productsCrud.getList();
        });

        $scope.$watch('products', function() {
            setTimeout(adjustProducts, 100);
        })

        $scope.getCategories = function(ids) {
            console.log(ids);
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
        }
    }])
    .controller("BasketController", function($scope) {
        $scope.test = 'Basket'
    })
    .controller("UsersController", ['$scope', '$route', '$routeParams', 'User', 'Group', function($scope, $route, $routeParams, User, Group) {
        var usersCrud = new CRUD($scope, User, 'users', 'user');

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
                var hash = $routeParams.hash;
                User.get({id: id, hash: hash}).$promise.then(function(r) {
                    $scope.user = r.user;
                }, function(r) {
                    window.location.href = BASEURL;
                })
            }
            $scope.renderAction = renderAction;
        }

        $scope.set_password = function(user) {
            if (user.password) {
                usersCrud.update(user).$promise.then(function(r) {
                    window.location.href = BASEURL;
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
})(jQuery);
