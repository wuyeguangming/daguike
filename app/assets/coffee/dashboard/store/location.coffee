angular.module('dashboard')
.controller('location', ['$scope', '$rootScope','http','commonService','commonAlbum','commonUploader','commonModal','$location',($scope, $rootScope, http, commonService,commonAlbum,commonUploader,commonModal,$location)->
    $scope.loc_communitys = []
    $scope.community = {}
    commonService.init 'location',->
        for location in data.location
            if location.level == 4
                $scope.loc_communitys.push(location) 
        $scope.community = $scope.loc_communitys[0]
])