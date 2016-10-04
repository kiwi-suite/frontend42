angular.module('frontend42').controller('SitemapController',['$scope', '$http', '$attrs', '$uibModal', '$window', function($scope, $http, $attrs, $uibModal, $window){
    $scope.isLoading = true;
    $scope.showOnline = true;
    $scope.showOffline = true;
    $scope.locale = $attrs.activeLocale;
    $scope.query = "";

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

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'sortModalContent.html',
                controller: 'ModalController',
                size: 'lg'
            });

            modalInstance.result.then(function () {
                saveTree();
            }, function () {
                loadTree();
            });
        }
    };
    $scope.sitemap = [];
    loadTree();

    $scope.addPage = function(formId, parentId) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'addPageModalContent.html',
            controller: 'AddPageModalController',
            size: 'lg',
            resolve: {
                data: function() {
                    data = {
                        url: $attrs.addUrl,
                        formId: formId,
                        parentId: 0
                    };

                    if (angular.isDefined(parentId)) {
                        data.parentId = parseInt(parentId);
                    }

                    return data;
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

    $scope.deleteCallback = function() {
        loadTree();
    }

}]);

angular.module('frontend42').controller('AddPageModalController', ['$scope', '$uibModalInstance', '$http', 'data', '$formService', function ($scope, $uibModalInstance, $http, data, $formService) {
    $scope.ok = function () {

        var pageName = $formService.get(data.formId, 'name');
        var pageTypeSelector = $formService.get(data.formId, 'pageTypeSelector');

        var check = false;
        if (angular.isDefined(pageName.value)) {
            if (pageName.value.length == 0) {
                pageName.errors = [''];
            } else {
                check = true;
            }
        }
        if (angular.isDefined(pageTypeSelector.value)) {
            if (pageTypeSelector.value.length == 0) {
                pageTypeSelector.errors = [''];
            } else {
                check = true;
            }
        }

        if (check !== true) {
            return;
        }
        $http({
                method: 'POST',
                url: data.url,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: {
                    pageSelector: parseInt(data.parentId),
                    pageTypeSelector: pageTypeSelector.value,
                    name: pageName.value
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
}]);

