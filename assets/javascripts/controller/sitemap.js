angular.module('frontend42').controller('SitemapController',['$scope', '$http', '$attrs', function($scope, $http, $attrs){
    $scope.isLoading = true;
    $scope.locale = $attrs.locale;
    $scope.sitemap = [];
    $scope.query = "";

    $scope.treeOptions = {
        accept: function(sourceNodeScope, destNodesScope) {
            var parent = destNodesScope.$parent;
            if (angular.isUndefined(parent.$modelValue)) {
                return false;
            }

            if (angular.isUndefined(sourceNodeScope.$modelValue)) {
                return false;
            }

            if (parent.$modelValue.isTerminal === true) {
                return false;
            }

            if (parent.$modelValue.pageTypes.length === 0) {
                return false;
            }

            if(!parent.$modelValue.pageTypes.hasOwnProperty(sourceNodeScope.$modelValue.pageType)) {
                return false;
            }

            return true;
        },
        dropped: function(event) {
            $scope.isLoading = true;
            $http.post($attrs.sortSaveUrl, $scope.sitemap).
            success(function(data) {
                loadTree();
            }).
            error(function() {
                $scope.isLoading = false;
            });
        }
    };

    var loadTree = function() {
        $scope.isLoading = true;
        var options = {
            locale: $scope.locale
        };

        $http.post($attrs.sitemapUrl, options).
            success(function(data) {
                $scope.sitemap = data;
                $scope.isLoading = false;
            }).
            error(function() {
                $scope.sitemap = [];
                $scope.isLoading = false;
            });
    };

    loadTree();

    $scope.change = function() {
        loadTree();
    };

    $scope.visible = function(item) {
        if (!$scope.query || $scope.query.length == 0) {
            return true;
        }

        if (item.title.toLowerCase().indexOf($scope.query.toLowerCase()) > -1) {
            return true;
        }

        if (item.items.length > 0) {
            for (var i = 0, len = item.items.length; i < len; i++) {
                if ($scope.visible(item.items[i]) == true) {
                    return true;
                }
            }
        }

        return false;
    };
    
    $scope.canAddPage = function(item) {
        if (item.isTerminal === true) {
            return false;
        }

        return (item.allowedPageTypes.length !== 0);
    }

    $scope.deleteCallback = function () {
        loadTree();
    }
}]);

