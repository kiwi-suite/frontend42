angular.module('admin42')
    .directive('formBlock', [function() {
        return {
            restrict: 'E',
            templateUrl: function(elem, attrs) {
                return attrs.template;
            },
            scope: {
                elementDataId: '@elementDataId'
            },
            controller: ['$scope', 'jsonCache', '$templateCache', '$formService', function($scope, jsonCache, $templateCache, $formService) {
                var elementData = jsonCache.get($scope.elementDataId);
                $scope.label = elementData.label;

                $scope.protoTypes = elementData.protoTypes;
                $scope.data = {
                    selectedProtoType: $scope.protoTypes[0]
                };

                $scope.treeOptions = {};
                $scope.sortingMode = false;
                $scope.elements = [];

                angular.forEach(elementData.elements, function(element){
                    var id = elementData.id + "-" + $scope.elements.length;

                    var templateName = 'element/form/stack/' + id + '.html';

                    var elementOrFieldsetData = angular.copy(jsonCache.get(element.elementDataId));
                    var elementOrFieldsetDataKey = 'element/form/value/' + id + '.json';

                    elementOrFieldsetData.id = id;
                    elementOrFieldsetData.name = elementData.name + '[' + elementOrFieldsetData.options.originalName + ']';

                    jsonCache.put(elementOrFieldsetDataKey, elementOrFieldsetData);
                    $templateCache.put(
                        templateName,
                        '<' + element.directive + ' element-data-id="' + elementOrFieldsetDataKey +'" template="'+ elementOrFieldsetData.template +'"></' + element.directive + '>'
                    );

                    $scope.elements.push({
                        formName: elementOrFieldsetData.name,
                        type: elementOrFieldsetData.options.stackType,
                        name: elementOrFieldsetData.options.fieldsetName,
                        nameEditing: false,
                        template: templateName,
                        elementDataId: elementOrFieldsetDataKey,
                        label: elementOrFieldsetData.label,
                        deleted: elementOrFieldsetData.options.fieldsetDeleted,
                        collapsed: $scope.sortingMode,
                        collapsedState: false,
                        nodes: []
                    });
                });

                $scope.collapse = function() {
                    angular.forEach($scope.elements, function(element){
                        if (element.collapsed === true) {
                            return;
                        }
                        element.collapsedState = element.collapsed;
                        element.collapsed = true;
                    });
                }

                $scope.expand = function() {
                    angular.forEach($scope.elements, function(element){
                        element.collapsed = element.collapsedState;
                    });
                }

                $scope.preventEnter = function(element, $event) {
                    if ($event.keyCode != 13) {
                        return;
                    }
                    element.nameEditing = false;
                    $event.preventDefault();
                }

                $scope.startSortingMode = function() {
                    if ($scope.sortingMode === false) {
                        $scope.$broadcast('$dynamic:sort-start');

                        $scope.sortingMode = true;
                        $scope.collapse();

                        return;
                    }

                    $scope.expand();
                    $scope.$broadcast('$dynamic:sort-stop');
                    $scope.sortingMode = false;
                };
                
                $scope.addTemplate = function () {
                    var id = elementData.id + "-" + $scope.elements.length;
                    var element = $scope.data.selectedProtoType;

                    var templateName = 'element/form/stack/' + id + '.html';

                    var elementOrFieldsetData = angular.copy(jsonCache.get(element.elementData));
                    var elementOrFieldsetDataKey = 'element/form/value/' + id + '.json';

                    elementOrFieldsetData.id = id;
                    elementOrFieldsetData.options.originalName = id;
                    elementOrFieldsetData.name = elementData.name + '[' + elementOrFieldsetData['options']['originalName'] + ']';

                    jsonCache.put(elementOrFieldsetDataKey, elementOrFieldsetData);
                    $templateCache.put(
                        templateName,
                        '<' + element.directive + ' element-data-id="' + elementOrFieldsetDataKey +'" template="'+ elementOrFieldsetData.template +'"></' + element.directive + '>'
                    );

                    $scope.elements.push({
                        formName: elementOrFieldsetData.name,
                        type: elementOrFieldsetData.options.stackType,
                        name: "",
                        nameEditing: false,
                        template: templateName,
                        elementDataId: elementOrFieldsetDataKey,
                        label: elementOrFieldsetData.label,
                        deleted: false,
                        collapsed: $scope.sortingMode,
                        collapsedState: false,
                        nodes: []
                    });
                }

                if (angular.isDefined(elementData.options.formServiceHash)) {
                    $formService.put(
                        elementData.options.formServiceHash,
                        elementData.name,
                        $scope.elementDataId
                    );
                }
            }]
        }
    }]);
