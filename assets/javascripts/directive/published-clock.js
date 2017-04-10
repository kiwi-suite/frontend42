angular.module('frontend42')
    .directive('publishedClock', [function() {
        return {
            restrict: 'E',
            templateUrl: 'published/clock.html',
            scope: {
                getPublishedFrom: "&publishedFrom",
                getPublishedUntil: "&publishedUntil",
                isPublished: "&isPublished"
            },
            controller: ['$scope', 'datetimeFilter', function($scope, datetimeFilter) {
                $scope.publishedFrom = datetimeFilter($scope.getPublishedFrom());
                $scope.publishedUntil = datetimeFilter($scope.getPublishedUntil());

                $scope.published = null;
                if ($scope.publishedUntil != "-" || $scope.publishedFrom != "-") {
                    $scope.published = $scope.isPublished();
                }

                $scope.getHtmlContent = function(publishedFromText, publishedUntilText) {
                    var html =  publishedFromText + ": " + $scope.publishedFrom + "<br>";
                    return html + publishedUntilText + ": " + $scope.publishedUntil;
                }
            }]
        };
    }]);
