angular.module('wx')
# todo 合并，功能统一
.controller('settingLocation', ['$scope', '$rootScope' ,'commonService','http','$location','$routeParams',($scope, $rootScope, commonService, http,$location,$routeParams) ->
    $rootScope.wx_navbar =
        id: 2
        ready: false
        btn: [
            text: '取消'
        ]
    $scope.selects = []
    $scope.room = ''
    $scope.title = '地址加载中，请稍候...'
    # $scope.subtitle = ''
    $scope.post = ->
        is_address = ('address' == $routeParams.from and $rootScope.cache.address?)
        $rootScope.wx_navbar.btn[0].text = '提交中，请稍后...'
        http.post('/wx/location',
            'is_address': is_address
            # 'parent': $scope.selects[5]['sid']
            # 'name':$scope.selects[6]
            'parent': $scope.selects[4]['sid']
            'sid': $scope.selects[5]['sid']
        ).then (res)->
            data.user = res.data.user
            if is_address
                ($rootScope.cache.address.selects[item.level] = item) for i,item of res.data.loc
            else
                data.user = res.data.user
            $rootScope.Back()
        ,(e)-> console.log e
    $scope.option =
        select: ''
        list : []
        item_class: 'col-xs-3 text-center'
        filter: (item)-> item.name
        init: (level)-> 
            $scope.option.list = []
            $scope.option.list.push(loc) for loc in data.location when(loc.level == level and loc.parent == $scope.selects[level-1].sid)
        click: (select,index)->
            # $scope.subtitle = '提交中，请稍后...'
            $scope.selects[select.level] = select
            if select.level ==5 #5 # else $scope.subtitle = '请选择楼层'
                $scope.post()
                # http.get('/common/location/son/'+select.sid).then (ret)->
                #     # $scope.subtitle = ''
                #     # $scope.option_room.init(ret.data) 
                #     $scope.option_building.init(ret.data) 
                # ,(e)-> 
                #     $scope.title = e
                #     # $scope.subtitle = ''
            else
                # $scope.subtitle = ''
                $scope.option.init(select.level+1) # todo
            $scope.option.select = ''
            $scope.title = ''
            $scope.title+= '/' + i.name for i in $scope.selects when i.level >3
            $scope.title = $scope.title[1..]
    # $scope.option_building =
    #     select: ''
    #     item_class: 'col-xs-3 text-center'
    #     list : []
    #     init: (loc)->
    #         $scope.option_building.list = loc.data.split(',')
    #     click: (select,index)->
    #         # $scope.selects[6] = select
    #         $scope.selects[5] = select
    #         $scope.title += '/'+select+'室'
    #         # $scope.subtitle = '提交中，请稍后...'
    #         $scope.post()

    commonService.init 'setting',-> 
        $scope.title = '请选择您的地址'
        # for i in [0..6]
        for i in [0..5]
            list = []
            for loc in data.location when loc.level == i
                # list.push loc if loc.level <= 5
                list.push loc if loc.level <= 4
            if 1 == list.length then $scope.selects[i] = list[0] else break #自动省略唯一选择
        $scope.option.init(i)

])
.controller('settingNolocation', ['$scope', '$rootScope' ,'commonService','http','$location','$routeParams',($scope, $rootScope, commonService, http,$location,$routeParams) ->
    return $location.path('/wx/setting/location') if data.user?
    $rootScope.wx_navbar.id = 0
    localStorage.clear()
    $rootScope.wx_cart.init()
    $scope.option =
        title: '地址加载中，请稍候...'
        select: ''
        list : []
        filter: (item)-> item.name
        init: (level)-> $scope.option.list.push(loc) for loc in data.location when loc.level == level 
        invalid : -> !$scope.option.select
        click : -> 
            http.post('/wx/setting/nolocation',{'location':$scope.option.select}).then (res)->
                $location.path '/'
            ,(e)-> console.log e
    commonService.init 'setting',-> 
        $scope.option.title = '请选择您的地址'
        $scope.option.init(4)
])
.controller('settingHongbao', ['$scope', '$rootScope' ,'commonService','http','$location','$routeParams',($scope, $rootScope, commonService, http,$location,$routeParams) ->
    $rootScope.wx_navbar =
        id: 2
    $scope.option =
        title: '我的红包'
        select: ''
        list : []
        class: 'hongbao'
        filter: (item)-> item.amount+'元(满'+item.condition+'元使用)'
        filter_subitem: (item)-> 
            info = if item.used then ' 已使用' else if item.invalid then ' 不在有效期' else  ''
            '有效期:' + item.time_start[0..9] + '至' + item.time_end[0..9] + info
        init: ()-> 
            now = new Date
            for hongbao in data.hongbao
                # hongbao.disenabled = !((new Date(hongbao.time_start) <= now <= new Date(hongbao.time_end)) and !hongbao.used)
                hongbao.invalid = !((new Date(hongbao.time_start.split(' ')[0]) <= now <= new Date(hongbao.time_end.split(' ')[0])) and !hongbao.used)
                hongbao.class = if hongbao.invalid then 'invalid' else 'valid'
                $scope.option.list.push(hongbao) 
        click : (hongbao)-> 
            if hongbao.order_id then $location.path('/wx/order/detail/'+hongbao.order_id)
    commonService.init 'hongbao',-> 
        $scope.option.title = '您暂时还没有红包' if !data.hongbao.length
        $scope.option.init()
])