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
    .controller("MainController", function($scope) {
        $scope.test = 'oi';
    })
    .controller("ProductsController", ["$scope", function($scope) {
        $scope.products = [
            {name: 'test', price: 12.5, quantity: 1},
            {name: 'test', price: 12},
            {name: 'test', price: 12.52},
            {name: 'test', price: 12.5},
            {name: 'test', price: 12.5},
            {name: 'test', price: 12.5},
            {name: 'test', price: 12.5},
            {name: 'test', price: 12.5},
        ];
    }])
    .controller("BasketController", function($scope) {
        $scope.test = 'Basket'
    })
})(jQuery);
