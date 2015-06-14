function Basket(localStorageService, scope, ProductResource, ClientResource){

    var $localStorage = localStorageService;

    var $scope = scope;

    var Product = ProductResource;

    var Client = ClientResource;

    var klass = this;

    var updateProduct = function(product) {
        var products = klass.getProducts();
        $.each(products, function(i, p) {
            if (product.id == p.id) {
                $.each(product, function(key, value) {
                    if (key != 'quantity') {
                        products[i][key] = value;
                    }
                });
                return false;
            }
        });
        klass.setProducts(products);
    }

    this.addProduct = function(product, quantity) {
        var products = klass.getProducts();
        if (!quantity) {
            quantity = 1;
        }
        var exists = false;
        $.each(products, function() {
            if (this.id == product.id) {
                this.quantity += quantity;
                exists = true;
                return false;
            }
        })
        if (!exists) {
            var newProduct = angular.copy(product);
            newProduct.quantity = quantity;
            products.push(newProduct);
        }
        klass.setProducts(products);
    }

    this.getProducts = function() {
        var products = $localStorage.get('basket_products');
        if (!products) {
            products = [];
        }
        return products;
    }

    this.setProducts = function(products) {
        $localStorage.set('basket_products', products);
        klass.updateTotals();
    }

    this.getProduct = function(id) {
        var products = klass.getProducts();
        var result = null;
        $.each(products, function() {
            if (this.id == id) {
                result = this;
                return false;
            }
        })

        return result;
    }

    this.removeProduct = function(id) {
        var products = klass.getProducts();
        $.each(products, function(index, product) {
            if (product.id == id) {
                products.splice(index, 1);
                return false;
            }
        })
        klass.setProducts(products);
    }

    this.changeQuantity = function(id, quantity) {
        if (!quantity || quantity <= 0) {
            quantity = 1;
        } else {
            quantity = parseInt(quantity);
        }
        var products = klass.getProducts();
        $.each(products, function() {
            if (this.id == id) {
                this.quantity = quantity;
                return false;
            }
        })
        klass.setProducts(products);
    }

    this.updateTotals = function() {
        var products = klass.getProducts();
        var basket = {total: 0, total_items: 0, quantity: 0};
        $.each(products, function() {
            basket.quantity += parseInt(this.quantity);
            total = this.quantity * this.price
            basket.total_items = Number((basket.total_items + total).toFixed(2));
        })
        var form = klass.getPaymentForm();
        if (form && form.interest > 0) {
            basket.total = Number((basket.total_items * (1 +(form.interest/100))).toFixed(2));
        } else {
            basket.total = basket.total_items;
        }
        $scope.basket = basket;
    }

    this.syncProducts = function() {
        var products = klass.getProducts();
        var basket = {total: 0, quantity: 0};
        $.each(products, function() {
            var p = this;
            Product.get({id: p.id}).$promise.then(function(r) {
                if (r.product.active) {
                    updateProduct(r.product);
                } else {
                    klass.removeProduct(p.id);
                }
                $scope.change_basket_products = Date.now();
            }, function() {
                klass.removeProduct(p.id);
                $scope.change_basket_products = Date.now();
            });
        })
    }

    this.setClient = function(client) {
        $localStorage.set('basket_client', client);
    }

    this.getClient = function() {
        return $localStorage.get('basket_client');
    }

    this.syncClient = function() {
        var client = klass.getClient();
        if (client) {
            Client.get({id: client.id}, function(r) {
                klass.setClient(r.client);
                $scope.change_basket_client = Date.now();
            }, function() {
                klass.setClient(null);
                $scope.change_basket_client = Date.now();
            })
        }
    }

    this.setPaymentForm = function(payment_form) {
        $localStorage.set('basket_payment_form', payment_form);
        klass.updateTotals();
    }

    this.getPaymentForm = function(payment_form) {
        return $localStorage.get('basket_payment_form');
    }

    this.clean = function() {
        $localStorage.set('basket_payment_form', null);
        $localStorage.set('basket_products', null);
        $localStorage.set('basket_client', null);
        $scope.change_basket_client = Date.now();
        $scope.change_basket_products = Date.now();
    }

    klass.updateTotals();
    klass.syncProducts();
    klass.syncClient();
}
