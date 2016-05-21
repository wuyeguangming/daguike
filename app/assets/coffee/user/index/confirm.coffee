# 注册邮箱确认
angular.module('user')
.controller('confirm', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'confirm'
])
