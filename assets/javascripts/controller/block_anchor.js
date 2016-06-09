angular.module('frontend42').controller('BlockAnchorController',['$scope', '$attrs','$uibModal', 'jsonCache', '$http', function($scope, $attrs, $uibModal, jsonCache, $http){
    $scope.inheritanceInfo = jsonCache.get($attrs.jsonDataId);

    $scope.saveAnchor = function(sitemapUrl, saveAnchorUrl, section, pageId) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'saveBlockAnchorModal.html',
            controller: 'SaveBlockAnchorModalController',
            size: 'lg',
            resolve: {
                saveAnchorUrl: function(){
                    return saveAnchorUrl;
                },
                sitemapUrl: function(){
                    return sitemapUrl;
                },
                section: function(){
                    return section;
                },
                pageId: function(){
                    return pageId;
                },
            }
        });

        modalInstance.result.then(function (data) {
            $scope.inheritanceInfo = data;
        }, function () {
            $scope.inheritanceInfo = null;
        });
    };

    $scope.cleanAnchor = function(cleanAnchorUrl, section, pageId) {
        $http({
            method: 'POST',
            url: cleanAnchorUrl,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            data: {
                sourcePageId: pageId,
                section: section
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            }
        })
            .success(function (data){
                $scope.inheritanceInfo = null;
            })
            .error(function (){
                $scope.inheritanceInfo = null;
            });
    };
}]);

angular.module('frontend42').controller('SaveBlockAnchorModalController', ['$scope', '$uibModalInstance', '$http', 'saveAnchorUrl', 'sitemapUrl', 'pageId', 'section', function ($scope, $uibModalInstance, $http, saveAnchorUrl, sitemapUrl, pageId, section) {
    $scope.targetPageId = 0;

    var loadTree = function() {
        $scope.isLoading = true;
        $http.post(sitemapUrl, {pageId: pageId, section: section}).
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
    loadTree();

    $scope.selectableStyle = function(item) {
        if ($scope.targetPageId == item.pageId) {
            return {
                'background-color': '#9DC482',
                'color': '#000',
                'cursor': 'pointer'
            }
        }

        if (item.selectable == true) {
            return {
                'cursor': 'pointer'
            };
        }

        return {
            'background-color': '#f6f8f8',
            'color': '#ccc'
        }
    };

    $scope.select = function(item) {
        if (item.selectable === false) {
            return;
        }
        $scope.targetPageId = item.pageId;
    };

    $scope.ok = function () {
        if ($scope.targetPageId === 0) {
            return;
        }

        $http({
            method: 'POST',
            url: saveAnchorUrl,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            data: {
                targetPageId: $scope.targetPageId,
                sourcePageId: pageId,
                section: section
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
