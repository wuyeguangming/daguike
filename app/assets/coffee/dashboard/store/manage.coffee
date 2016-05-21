angular.module('dashboard')
.controller('manage', ['$scope', '$rootScope','http','commonService','commonAlbum','commonUploader','commonModal','$location',($scope, $rootScope, http, commonService,commonAlbum,commonUploader,commonModal,$location)->
    $scope.page = {}
    commonService.init 'manage',->
        $scope.page = $rootScope.data.page
        $scope.del = (goods)-> commonModal.confirm ('确定要删除"'+goods.name+'"吗？'),->
            http.post('/dashboard/store/manage-del',
                goods_id: goods.id,
                page: $scope.page.current_page
            ).then (res)->
                (return location.href = '/dashboard/store/manage/'+(res.data.page.last_page or '')) if !res.data.page.data.length
                $scope.page = res.data.page
                commonModal.alert res.info
            ,(e)->
                commonModal.alert e

])