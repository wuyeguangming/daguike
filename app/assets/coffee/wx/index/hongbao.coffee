angular.module('wx')
.controller('hongbao', ['$scope', '$rootScope' ,'commonService','$filter','http','$location','$timeout',($scope, $rootScope, commonService, $filter,http,$location,$timeout) ->
    $rootScope.wx_navbar =
        id: 2
    $scope.option =
        title: '请选择红包'
        subtitle: 
            text: '当前订单总额：'+$filter('currency')($rootScope.wx_cart.cache.goods_amount,'')+'元'
        select: ''
        list : []
        filter: (item)-> 
            used = if item.used then '[已使用]' else ''
            return used + item.amount+'元(满'+item.condition+'元使用)'
        filter_subitem: (item)-> 
            now = new Date
            invalid = if !(new Date(item.time_start.split(' ')[0]) <= now <= new Date(item.time_end.split(' ')[0])) then '[不在有效期]' else ''
            return invalid + '有效期：' + item.time_start[0..9] + '至' + item.time_end[0..9]
        init: ()-> 
            now = new Date
            for hongbao in data.hongbao
                invalid = !$filter('wx_cartHongbaoValid')(hongbao)# !((new Date(hongbao.time_start) <= now <= new Date(hongbao.time_end)) and !hongbao.used and ($rootScope.wx_cart.cache.amount >= hongbao.condition))
                hongbao.class = if invalid then 'invalid' else 'valid'
                $scope.option.list.push(hongbao) 
        click : -> 
            now = new Date
            return alert('该红包已被使用过了') if $scope.option.select.used
            return alert('该红包不在有效期内') if !(new Date($scope.option.select.time_start.split(' ')[0]) <= now <= new Date($scope.option.select.time_end.split(' ')[0]))
            return alert('该红包需满'+$scope.option.select.condition+'才能使用') if $rootScope.wx_cart.cache.goods_amount < $scope.option.select.condition
            $rootScope.wx_cart.hongbao = $scope.option.select
            $rootScope.wx_cart.update()
            $rootScope.Back()
    commonService.init 'hongbao',-> 
        $scope.option.title = '您暂时还没有红包' if !data.hongbao.length
        $scope.option.init()

])