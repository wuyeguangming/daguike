angular.module('dashboard')
.controller('settings', ['$scope', '$rootScope','http','commonService',($scope, $rootScope, http, commonService) ->
    commonService.init 'settings',->
        $scope.loc = 
            loc_province  : $rootScope.data.user.loc_province
            loc_city      : $rootScope.data.user.loc_city
            loc_district  : $rootScope.data.user.loc_district
            loc_community : $rootScope.data.user.loc_community
        $rootScope.settings.submit = ->
            $rootScope.settings.is_sent = false
            $rootScope.settings.submit_errors = []
            $rootScope.settings.btn_info = '提交中，请稍后...'
            http.post('/dashboard/settings',{
                # gender: data.gender
                # birthday: data.birthday
                loc_province  : $scope.loc.loc_province
                loc_city      : $scope.loc.loc_city
                loc_district  : $scope.loc.loc_district
                loc_community : $scope.loc.loc_community
            }).then (res)->
                $rootScope.settings.btn_info = '修改' 
                $rootScope.settings.submit_info = ['修改成功！']
            ,(e)->
                e = [e] if _.isString e
                $rootScope.settings.submit_info = e
                $rootScope.settings.btn_info = '修改' 
])
