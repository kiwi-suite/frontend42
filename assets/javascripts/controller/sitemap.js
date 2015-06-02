angular.module('frontend42').controller('SitemapController',['$scope', '$http', '$attrs', '$modal', function($scope, $http, $attrs, $modal){
    $scope.isLoading = true;
    $scope.showOnline = true;
    $scope.showOffline = true;
    $scope.locale = $attrs.activeLocale;

    var requestUrl = $attrs.requestUrl;
    var saveUrl = $attrs.saveUrl;

    var loadTree = function() {
        $scope.isLoading = true;
        var options = {
            showOnline: $scope.showOnline,
            showOffline: $scope.showOffline,
            locale: $scope.locale
        }
        $http.post(requestUrl, options).
            success(function(data) {
                $scope.sitemap = data;
                $scope.isLoading = false;
            }).
            error(function() {
                $scope.sitemap = [];
                $scope.isLoading = false;
            });
    };
    $scope.loadTree = loadTree;

    var saveTree = function (){
        $scope.isLoading = true;
        $http.post(saveUrl, $scope.sitemap).
            success(function(data) {
                $scope.isLoading = false;
            }).
            error(function() {
                loadTree();
            });
    };

    $scope.treeOptions = {
        accept: function(sourceNodeScope, destNodesScope) {
            var parent = destNodesScope.$parent;
            if (angular.isUndefined(parent.$modelValue)) {
                return true;
            }
            if (angular.isUndefined(parent.$modelValue.droppable)) {
                return true;
            }

            return parent.$modelValue.droppable;
        },
        dropped: function() {
            if ($scope.sitemap.length == 0) {
                return;
            }

            var modalInstance = $modal.open({
                animation: true,
                templateUrl: 'sortModalContent.html',
                controller: 'ModalController',
                size: 'lg'
            });

            modalInstance.result.then(function () {
                saveTree();
            }, function () {
            });
        }
    };
    $scope.sitemap = [];
    loadTree();

    $scope.addPage = function() {
        var modalInstance = $modal.open({
            animation: true,
            templateUrl: 'addPageModalContent.html',
            controller: 'AddPageModalController',
            size: 'lg',
            resolve: {
                addSitemapUrl: function(){
                    return $attrs.addUrl;
                }
            }
        });

        modalInstance.result.then(function () {

        }, function () {

        });
    };

}]);

angular.module('frontend42').controller('AddPageModalController', ['$scope', '$modalInstance', '$http', 'addSitemapUrl', function ($scope, $modalInstance, $http, addSitemapUrl) {
    $scope.ok = function () {
        console.log($scope.formElement);
        $http({
                method: 'POST',
                url: addSitemapUrl,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: {
                    page_selector: $scope.formElement.page_selector,
                    page_type_selector: $scope.formElement.page_type_selector
                },
                transformRequest: function(obj) {
                    var str = [];
                    for(var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                }
            })
            .success(function (){
                //$modalInstance.close();
            })
            .error(function (){

            });

    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}]);

