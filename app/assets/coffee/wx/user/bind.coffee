angular.module('wx')
.controller('bind', ['$scope', '$rootScope' ,'commonService','$filter','http','$location',($scope, $rootScope, commonService, $filter,http,$location) ->
    $scope.rememberMe = true;
])