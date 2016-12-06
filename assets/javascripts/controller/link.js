angular.module('frontend42')
    .controller('SitemapLinkController',['$scope', '$attrs', '$http', function($scope, $attrs, $http){
            $scope.selectedPage = 0;
            $scope.locale = $attrs.activeLocale;
            $scope.query = "";

            var initialValue = $scope.link.getValue();
            if (initialValue != null) {
                $scope.selectedPage = initialValue.id;
                $scope.locale = initialValue.locale;
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

            $scope.findNodes = function(){

            };

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
