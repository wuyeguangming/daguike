angular.module('wx')
.controller('me', ['$scope', '$rootScope' ,'commonService','http',($scope, $rootScope, commonService, http) ->
    $rootScope.wx_navbar.id = 1
])