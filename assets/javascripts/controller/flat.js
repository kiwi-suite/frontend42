angular.module('frontend42').controller('FlatController',['$scope', '$http', '$attrs', '$modal', '$window', function($scope, $http, $attrs, $modal, $window){
    $scope.addPage = function() {
        var modalInstance = $modal.open({
            animation: true,
            templateUrl: 'addFlatModalContent.html',
            controller: 'AddFlatModalController',
            size: 'lg',
            resolve: {
                addSitemapUrl: function(){
                    return $attrs.addUrl;
                },
                locale: function(){
                    return $scope.flat.locale;
                }
            }
        });

        modalInstance.result.then(function (data) {
            if (angular.isDefined(data.url)) {
                $window.location.href = data.url;
            }

        }, function () {

        });
    };
}]);

angular.module('frontend42').controller('AddFlatModalController', ['$scope', '$modalInstance', '$http', 'addSitemapUrl', 'locale', function ($scope, $modalInstance, $http, addSitemapUrl, locale) {
    $scope.ok = function () {
        $http({
            method: 'POST',
            url: addSitemapUrl,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            data: {
                locale: locale,
                name: $scope.formElement.name
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            }
        })
            .success(function (data){
                $modalInstance.close(data);
            })
            .error(function (){

            });

    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}]);
