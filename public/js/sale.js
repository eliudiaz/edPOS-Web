(function (angular) {
    var app = angular.module('tutapos', ['ui.bootstrap']);

    app.controller("SearchItemCtrl", ['$scope', '$http', function ($scope, $http) {
        $scope.customer = null;
        $scope.customerRef = null;
        $scope.items = [];
        $http.get('api/item').success(function (data) {
            $scope.items = data;
        });
        $scope.saletemp = [];
        $scope.newsaletemp = {};
        $http.get('api/saletemp').success(function (data, status, headers, config) {
            $scope.saletemp = data;
        });
        $scope.customers = [];
        $http.get('api/customers').success(function (data) {
            $scope.customers = data;
        });
        $scope.selectCustomer = function () {
            $scope.customerRef = $scope.customers.filter((c) => c.id == $scope.customer)[0];
        };
        $scope.addSaleTemp = function (item, newsaletemp) {
            item.discount = 0;
            if ($scope.customerRef != null && $scope.customerRef.discount_percentage && $scope.customerRef.discount_percentage > 0) {
                let percentage = $scope.customerRef.discount_percentage / 100;
                item.discount = item.selling_price * percentage;
            }
            $http.post('api/saletemp', {
                item_id: item.id,
                cost_price: item.cost_price,
                selling_price: item.selling_price,
                discount: item.discount
            }).success(function (data, status, headers, config) {
                $scope.saletemp.push(data);
                $http.get('api/saletemp').success(function (data) {
                    $scope.saletemp = data;
                });
            });
        };
        $scope.findCustomer = function (customers, typedValue) {
            return customers.filter(function (customer) {
                matchAccount = customer.account.indexOf(typedValue) != -1;
                matchName = customer.name.indexOf(typedValue) != -1;
                return matchAccount || matchName;
            });
        };

        $scope.updateSaleTemp = function (newsaletemp) {
            $http.put('api/saletemp/' + newsaletemp.id, {
                quantity: newsaletemp.quantity,
                selling_price: newsaletemp.selling_price,
                discount: newsaletemp.discount,
                total_cost: newsaletemp.item.cost_price * newsaletemp.quantity,
                total_selling: (newsaletemp.selling_price * newsaletemp.quantity) - newsaletemp.discount,
            }).success(function (data, status, headers, config) {

            });
        };
        $scope.removeSaleTemp = function (id) {
            $http.delete('api/saletemp/' + id).success(function (data, status, headers, config) {
                $http.get('api/saletemp').success(function (data) {
                    $scope.saletemp = data;
                });
            });
        };
        $scope.sum = function (list) {
            var total = 0;
            angular.forEach(list, function (newsaletemp) {
                if (newsaletemp.item) {
                    total += parseFloat(newsaletemp.item.selling_price * newsaletemp.quantity) - newsaletemp.discount;
                }
            });
            return total;
        }

    }]);
})(angular);