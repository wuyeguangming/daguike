angular.module('dashboard')
.controller('index', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'index',->
])
