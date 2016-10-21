angular.module('frontend42').controller('SitemapController',['$scope', '$http', '$attrs', function($scope, $http, $attrs){
    $scope.isLoading = true;
    $scope.locale = $attrs.locale;
    $scope.sitemap = [];
    $scope.query = "";

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
}]);

