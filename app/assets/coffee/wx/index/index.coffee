angular.module('wx')
.controller('index', ['$scope', '$rootScope' ,'commonService','commonScroll','http','$timeout','$location',($scope, $rootScope, commonService,commonScroll,http,$timeout,$location) ->
    $rootScope.wx_navbar = $rootScope.wx_navbar || {}
    $rootScope.wx_navbar.ready = false
    # $scope.show_all = false
    # $scope.show_index = true
    $scope.goods_list = []
    $scope.goods_show = []
    $scope.albums = []
    $scope.album_titles = []
    $rootScope.__index_scroll_pos = $rootScope.__index_scroll_pos or document.body.scrollTop
    $scope.show_num = 4
    $scope.loading_num = 0
    $scope.networkType = '2g'
    $scope.show_goods = (id)->
        $rootScope.wx_navbar.ready = false
        $rootScope.__index_scroll_pos = document.body.scrollTop
        $location.path(U.goods(id))
    window.__onload__ = (id)->
        $scope.goods_show[id] = true
        $scope.loading_num = $scope.loading_num + 1
        $timeout ->
            document.body.scrollTop = $rootScope.__index_scroll_pos or 0
        , 1
        $scope.$apply()
    commonScroll.isCenter ->
        return if $rootScope.__controller != 'index'
        if ($scope.networkType == '2g')
            $scope.show_num = $scope.show_num + 2
            $scope.show_num = data.goods_list.length if $scope.show_num > data.goods_list.length
            $scope.$apply()

    commonService.init 'index',->
        # $scope.show_all = true
        for album in data.albums
            $scope.albums[album.id] = album
            $scope.goods_list[album.sort] = []
            $scope.album_titles[album.sort] = album
        $scope.goods_list[$scope.albums[goods.album_id].sort].push goods for goods in data.goods_list
        wx.ready ->
            wx.getNetworkType
                success: (res) ->
                    $scope.networkType = res.networkType # 返回网络类型2g，3g，4g，wifi
                    ($scope.show_num = data.goods_list.length) if ('2g'!=$scope.networkType)
                    $scope.$apply()
        $rootScope.wx_config data.wx
    ,((e)->console.log(e))
    ,true
])