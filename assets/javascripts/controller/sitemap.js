angular.module('frontend42').controller('SitemapController',['$scope', '$http', '$attrs', '$modal', '$window', function($scope, $http, $attrs, $modal, $window){
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

            var modalInstance = $modal.open({
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

    $scope.addSubPage = function(parentId) {
        var modalInstance = $modal.open({
            animation: true,
            templateUrl: 'addSubPageModalContent.html',
            controller: 'AddPageModalController',
            size: 'lg',
            resolve: {
                addSitemapUrl: function(){
                    return $attrs.addUrl;
                },
                parentId: function(){
                    return parentId;
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

    $scope.addPage = function(parentId) {
        var modalInstance = $modal.open({
            animation: true,
            templateUrl: 'addPageModalContent.html',
            controller: 'AddPageModalController',
            size: 'lg',
            resolve: {
                addSitemapUrl: function(){
                    return $attrs.addUrl;
                },
                parentId: function(){
                    return null;
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

angular.module('frontend42').controller('AddPageModalController', ['$scope', '$modalInstance', '$http', 'addSitemapUrl', 'parentId', function ($scope, $modalInstance, $http, addSitemapUrl, parentId) {
    $scope.ok = function () {
        if (parentId !== null) {
        } else {
            parentId = $scope.formElement.page_selector;
        }
        $http({
                method: 'POST',
                url: addSitemapUrl,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: {
                    page_selector: parentId,
                    page_type_selector: $scope.formElement.page_type_selector,
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

