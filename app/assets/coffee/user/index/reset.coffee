# 重置密码
angular.module('user')
.controller('reset', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'reset',->
        $scope.invalid_password = ()-> $rootScope.invalid_password($scope.reset.password)
        $rootScope.reset.submit = ->
            $rootScope.reset.submit_errors = []
            $rootScope.reset.btn_info = '提交中，请稍后...'
            http.post('/user/reset',{
                token: $rootScope.data.token
                password: $rootScope.reset.password
                password_confirmation: $rootScope.reset.password_confirmation
            }).then (res)->
                $rootScope.reset.is_sent = true
            ,(e)->
                e = [e] if _.isString e
                $rootScope.reset.submit_errors = e
                $rootScope.reset.btn_info = '重置密码' 
])