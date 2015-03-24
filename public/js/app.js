(function($) {
    var app = angular.module("forcaDeVendas", ['ngRoute']);

    app.config(function($routeProvider, $locationProvider) {
        $routeProvider
            .when('/basket', {
                controller: 'BasketController',
                templateUrl: 'basket.html',
            })
            .when('/products', {
                controller: 'ProductsController',
                templateUrl: 'products.html',
            })
            .otherwise({
                controller: 'MainController',
                templateUrl: 'main.html',
            });

    })
    .controller("MainController", function($scope) {
        $scope.test = 'oi';
    })
    .controller("ProductsController", ["$scope", function($scope) {
        $scope.products = [
            {name: 'test'},
            {name: 'test'},
            {name: 'test'},
            {name: 'test'},
            {name: 'test'},
            {name: 'test'}
        ];
    }])
    .controller("BasketController", function($scope) {
        $scope.test = 'Basket'
    })
})(jQuery);
