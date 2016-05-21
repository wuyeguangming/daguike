# 登录
angular.module('user')
.controller('login', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'login',->
        $rootScope.login.submit = ->
            $rootScope.login.submit_errors = []
            $rootScope.login.btn_info = '提交中，请稍后...'
            http.post('/user/login',{
                username: $rootScope.login.username
                password: $rootScope.login.password
            }).then (res)->
                location.href = '/user'
            ,(e)->
                e = [e] if _.isString e
                $rootScope.login.submit_errors = e
                $rootScope.login.btn_info = '重新登录'   
])
