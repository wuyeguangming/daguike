angular.module('dashboard')
.controller('password', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'password',->
        $rootScope.password.submit = ->
            $rootScope.password.submit_error = false
            $rootScope.password.submit_info = []
            $rootScope.password.btn_info = '提交中，请稍后...'
            http.post('/dashboard/password',{
                username: $rootScope.data.user.username
                email: $rootScope.data.user.email
                password_old: $rootScope.password.password_old
                password_new: $rootScope.password.password_new
                password_confirmation: $rootScope.password.password_confirmation
            }).then (res)->
                $rootScope.password.is_sent = true
                $rootScope.password.btn_info = '修改密码' 
                $rootScope.password.submit_info = ['修改成功！']
            ,(e)->
                e = [e] if _.isString e
                $rootScope.password.submit_error = true
                $rootScope.password.submit_info = e
                $rootScope.password.btn_info = '修改密码' 
])
