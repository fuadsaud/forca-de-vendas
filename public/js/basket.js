function Basket(localStorageService, scope, ProductResource){

    var $localStorage = localStorageService;

    var $scope = scope;

    var Product = ProductResource;

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
        var basket = {total: 0, quantity: 0};
        $.each(products, function() {
            basket.quantity += parseInt(this.quantity);
            total = this.quantity * this.price
            basket.total = Number((basket.total + total).toFixed(2));
        })
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

    klass.updateTotals();
    klass.syncProducts();
}
