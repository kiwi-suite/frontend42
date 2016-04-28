angular.module('frontend42')
    .controller('ChangePageTypeController',['$scope', '$attrs', '$uibModal', '$window', function($scope, $attrs, $uibModal, $window){

        $scope.change = function() {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'changePageTypeModal.html',
                controller: 'ChangePageTypeModalController',
                size: 'lg',
                resolve: {
                    changeUrl: function(){
                        return $attrs.changeUrl;
                    }
                }
            });

            modalInstance.result.then(function (data) {
                if (angular.isDefined(data.redirect)) {
                    $window.location.href = data.redirect;
                }
            }, function () {
            });
        };
    }]
);

angular.module('frontend42')
    .controller('ChangePageTypeModalController', [
        '$scope',
        '$modalInstance',
        '$http',
        'changeUrl',
        function ($scope, $uibModalInstance, $http, changeUrl) {
            $scope.ok = function () {
                $http({
                    method: 'POST',
                    url: changeUrl,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    data: {
                        page_type: $scope.formElement.page_type_selector,
                    },
                    transformRequest: function(obj) {
                        var str = [];
                        for(var p in obj)
                            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                        return str.join("&");
                    }
                })
                    .success(function (data){
                        $uibModalInstance.close(data);
                    })
                    .error(function (){

                    });

            };

            $scope.cancel = function () {
                $uibModalInstance.dismiss('cancel');
            };
        }
    ]
);
