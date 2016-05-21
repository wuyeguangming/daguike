angular.module('dashboard')
.controller('create', ['$scope', '$rootScope','http','commonService','$location',($scope, $rootScope, http, commonService, $location) ->
    $scope.CREATE_MIN = 2
    $scope.CREATE_MAX = 20
    $scope.invalid_name = -> (/^[0-9]+$/).test($scope.create.name) 
    commonService.init 'create',->
        location.href = '/dashboard/index/index' if $rootScope.data.store
        $scope.loc = 
            loc_province  : $rootScope.data.user.loc_province
            loc_city      : $rootScope.data.user.loc_city
            loc_district  : $rootScope.data.user.loc_district
            loc_community : $rootScope.data.user.loc_community
        $rootScope.create.submit = ->
            $rootScope.create.is_sent = false
            $rootScope.create.submit_error = ''
            $rootScope.create.submit_success = ''
            $rootScope.create.btn_info = '提交中，请稍后...'
            http.post('/dashboard/store/create',{
                name: $rootScope.create.name
            }).then (res)->
                $rootScope.create.btn_info = '创建' 
                $rootScope.create.submit_error = ''
                $rootScope.create.submit_success = '创建成功！'
                $location.path('/dashboard/store/album')
            ,(e)->
                $rootScope.create.submit_error = e
                $rootScope.create.submit_success = ''
                $rootScope.create.btn_info = '创建' 
])
