angular.module('wx')
.controller('gamesHongbao', ['$rootScope','$scope','$location','commonService','http',($rootScope,$scope,$location,commonService,http)->
    $rootScope.wx_navbar =
        id: 2
        ready: true
        btn: [ 
            text: '返回'
            class: 'btn-default'
        ,
            text: '所有红包'
            class: 'btn-default'
            click: ->
                $location.path('/wx/setting/hongbao')
        ]
    $scope.REFRESH_LIMIT = 5
    $scope.shua = ->
        http.post('/wx/games/hongbao',{}).then (ret)->
            $scope.hongbao = ret.data.hongbao
        ,(e)->
            $scope.info = e.info
    $scope.info = '刷红包'
    commonService.init 'hongbao',->
        $scope.hongbao = data.hongbao if data.hongbao?
])