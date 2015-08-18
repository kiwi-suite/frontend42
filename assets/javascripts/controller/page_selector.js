angular.module('frontend42').controller('PageSelectorController',['$scope', '$attrs','jsonCache', function($scope, $attrs, jsonCache){
    $scope.page = {};
    $scope.locale = $attrs.activeLocale;
    $scope.pages = jsonCache.get($attrs.jsonDataId)[$scope.locale];

    $scope.changeLocale = function() {
        $scope.pages = jsonCache.get($attrs.jsonDataId)[$scope.locale];
        $scope.page = {};
    }
}]);
