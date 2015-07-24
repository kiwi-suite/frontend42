angular.module('frontend42')
    .controller('LinkSitemapController',['$scope', '$attrs', '$http', function($scope, $attrs, $http){
        $scope.selectedPage = 0;
        $scope.locale = $attrs.activeLocale;

        var inititalValue = $scope.link.getValue();
        if (inititalValue != null) {
            $scope.selectedPage = inititalValue.id;
            $scope.locale = inititalValue.locale;
        }

        var loadTree = function() {
            $scope.isLoading = true;
            $http.post($attrs.requestUrl, {locale: $scope.locale}).
                success(function(data) {
                    $scope.sitemap = data;
                    $scope.isLoading = false;
                }).
                error(function() {
                    $scope.sitemap = [];
                    $scope.isLoading = false;
                });
        };
        $scope.sitemap = [];
        $scope.loadTree = loadTree;
        loadTree();

        $scope.selectableStyle = function(item) {
            if ($scope.selectedPage == item.pageId) {
                return {
                    'background-color': '#9DC482',
                    'color': '#000',
                    'cursor': 'pointer'
                }
            }

            return {
                'cursor': 'pointer'
            };
        };

        $scope.select = function(item) {
            $scope.selectedPage = item.pageId;
            $scope.link.setValue({
                id: $scope.selectedPage,
                locale: $scope.locale
            });
        };
    }]
);