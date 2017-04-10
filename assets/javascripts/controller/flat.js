angular.module('frontend42').controller('FlatController',['$scope', '$http', '$attrs', function($scope, $http, $attrs){
    $scope.flat = {
        locale: $attrs.locale
    };
}]);

