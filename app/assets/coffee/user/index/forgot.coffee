# 忘记密码
angular.module('user')
.controller('forgot', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'forgot',->
        $rootScope.forgot.submit = ->
            $rootScope.forgot.submit_errors = []
            $rootScope.forgot.btn_info = '提交中，请稍后...'
            http.post('/user/forgot',{
                email: $rootScope.forgot.email
            }).then (res)->
                $rootScope.forgot.is_sent = true
            ,(e)->
                e = [e] if _.isString e
                $rootScope.forgot.submit_errors = e
                $rootScope.forgot.btn_info = '找回密码' 
])