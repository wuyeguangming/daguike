# 注册
angular.module('user')
.controller('create', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http,commonService) ->
    commonService.init 'create',->
        $scope.loc = {}
        $scope.invalid_username = ()-> (/^[0-9]+$/).test($scope.create.username) 
        $scope.invalid_password = ()-> $rootScope.invalid_password($scope.create.password)

        $rootScope.create.submit = ->
            $rootScope.create.submit_errors = []
            $rootScope.create.btn_info = '提交中，请稍后...'
            http.post('/user/index',{
                username: $rootScope.create.username
                email:    $rootScope.create.email
                password: $rootScope.create.password
                password_confirmation: $rootScope.create.password_confirmation
                loc_province  : $scope.loc.loc_province
                loc_city      : $scope.loc.loc_city
                loc_district  : $scope.loc.loc_district
                loc_community : $scope.loc.loc_community
            }).then (res)->
                $rootScope.create.email_confirm = true
            ,(e)->
                e = [e] if _.isString e
                $rootScope.create.submit_errors = e
                $rootScope.create.btn_info = '立即注册'
])