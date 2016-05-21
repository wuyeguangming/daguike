angular.module('dashboard')
.controller('refund', ['$scope', '$rootScope','http','commonService','commonAlbum','commonUploader','commonModal','$location',($scope, $rootScope, http, commonService,commonAlbum,commonUploader,commonModal,$location)->
    $scope.update = -> 
        commonService.init 'refund',->
            $scope.post = (order, agree)->
                http.post('/dashboard/store/refund',
                    id: order.id
                    agree: agree
                ).then (res)->
                    $scope.update()
                ,(e)->
                    console.log e
    $scope.update()
])