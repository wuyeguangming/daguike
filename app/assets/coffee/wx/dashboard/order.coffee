angular.module('wx')
.filter 'orderAmount',-> 
    return (order)->  
        return (if order.discount >= 0.01 then ('优惠'+order.discount+'元 ') else '') + '合计' + order.amount + '元'
.filter 'orderPay',-> 
    return (order)-> (if order.pay_type then '-' else '￥')+order.amount+' 优惠:'+order.discount+(if order.pay_type then ' 到付' else '')
.filter 'orderAddress',-> 
    # return (order)-> order.location.loc_community.name+order.location.loc_building.name+order.location.loc_room.name
    return (order)-> order.location.loc_community.name+order.location.loc_building.name+order.address.loc_detail
.filter 'orderTime',-> 
    return (order)-> 
        ret = order.delivery_time[5..15]
        now = new Date
        dt = new Date(order.delivery_time)
        if dt - now > 30*60*1000
            ret = '[预约]' + ret
        if dt.getDate() > now.getDate()
            ret = '[明天]' + ret
        return ret #"2015-03-25 00:11:51" order.delivery_time[8..9]+'/'+
.filter 'orderStatus',-> 
    # return (order)-> ['已支付','货到付款'][order.pay_type]
    return (order)-> ['已取消','等待支付','下单成功','发货中','已发货','交易成功','申请退款中','退款成功','退款失败','无人收货'][order.status]
.controller('orderIndex', ['$scope', '$rootScope' ,'commonService','http','$filter','$location',($scope, $rootScope, commonService, http, $filter,$location) ->
    data.order = []
    $rootScope.wx_navbar =
        id: 2
        ready: false
        btn: [
            text: '首页'
            click: -> location.href = '/'
        ,
            text: '上一页'
            click: -> 
                p = parseInt(data.page.current_page)-1
                p = 1 if p<1
                http.get('/wx/dashboard/order-page/'+p).then (ret)->
                    window.data = ret
                    $scope.index.update()
                # ,(e)->$scope.index.btn_pre = '点击查看更多'
                ,(e)->console.log e
        ,
            text: '下一页'
            click: -> 
                p = parseInt(data.page.current_page)+1
                return alert('已经到底了') if p > data.page.last_page
                http.get('/wx/dashboard/order-page/'+p).then (ret)->
                    window.data = ret
                    $scope.index.update()
                ,(e)->console.log e
        ]
    $scope.index =
        title : '所有订单'
        btn_more: '点击查看更多'
        order : []
        order_goods : []
        scan: ()->
            console.log 11
            wx.scanQRCode
                needResult: 0 # 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode"] # 可以指定扫二维码还是一维码，默认二者都有
                success:  (res) ->
                    $location.path('/wx/dashboard/order-detail/'+res) # 当needResult 为 1 时，扫码返回的结果
        update: ()-> 
            # $scope.index.order.push order for order in data.order
            # $scope.index.order_goods.push order_goods for order_goods in data.order_goods
            $scope.index.order = []
            $scope.index.order_goods = []
            for order in data.page.data
                $scope.index.order.push order 
                $scope.index.order_goods.push order.order_goods
            for order_goods,index in $scope.index.order_goods
                $scope.index.order[index].order_goods = []
                for i in order_goods
                    item =
                        image: $rootScope.U.img(i.goods_image,'sm')
                    item.p = [i.goods_name,$filter('currency')(i.sku_price,"￥"),i.sku_value,'X'+i.num]
                    item.class = []
                    $scope.index.order[index].order_goods.push item
        click: (select)-> $location.path('/wx/dashboard/order-detail/'+select.id)
    commonService.init 'order',->
        $rootScope.wx_navbar.ready = true
        $scope.index.update()
])
.controller('orderDetail', ['$scope', '$rootScope' ,'commonService','http','$filter','$timeout','$location',($scope, $rootScope, commonService, http, $filter,$timeout,$location) ->
    $rootScope.wx_navbar =
        id: 2
        ready: false
        btn: [
            click: -> $rootScope.Back() #$location.path('/wx/order')
            hide: -> !data.order
        ]
    commonService.init 'order',->
        $rootScope.wx_navbar.ready = true
        pay_confirm_cnt = 0
        pay_confirm = (id)->
            return if 'orderDetail' != $rootScope._controller
            $rootScope.wx_navbar.btn[1].text = '订单确认中...'
            $rootScope.wx_navbar.btn[1].click = ''
            $timeout ->
                if pay_confirm_cnt++ > 10
                    pay_confirm_cnt = 0
                    $rootScope.wx_navbar.btn[1].click = -> pay_confirm(id)
                    return $rootScope.wx_navbar.btn[1].text = '确认超时，重新确认' 
                http.get('/wx/order/detail/'+id).then (ret)->
                    return pay_confirm(id) if (!ret.order? or ret.order.status == 1)
                    $rootScope.data = ret
                    $rootScope.wx_navbar.btn[1].class = 'btn-success'
                    $rootScope.wx_navbar.btn[1].text = '下单成功！'
                    $rootScope.wx_navbar.btn[1].click = -> $rootScope.Goto('/')
                ,(e)-> pay_confirm(id)
                    # $rootScope.wx_navbar.btn[1].text = '支付失败！'
                    # $rootScope.$apply()
            , 1000

        data.address['location'] = data.location
        $scope.order_goods = []
        for i in data.order_goods
            item =
                href: $rootScope.U.goods(i.goods_id)
                image: $rootScope.U.img(i.goods_image,'sm')
            item.p = [i.goods_name,$filter('currency')(i.sku_price,"￥"),i.sku_value,'X'+i.num]
            item.class = []
            $scope.order_goods.push item
        
        post = (status)->
            http.post('/wx/dashboard/order-detail',{id:data.order.id,status:status}).then (res)->
                data.order.status = res.data.order.status
            ,(e)->alert JSON.stringify(e)
        # if 3 == data.order.status #and !data.order.pay_type
        $rootScope.wx_navbar.btn[3] =
            text: '完成'
            class: 'btn-default'
            click: -> post(5) if confirm '确定货物已送达吗？'
        $rootScope.wx_navbar.btn[2] =
            text: '退货'
            class: 'btn-default'
            click: ->post(6) if confirm '确定退货吗？'
        # todo 同意退货退款

        $rootScope.wx_navbar.btn[1] =
            text: '无人'
            class: 'btn-default'
            click: ->
            click: -> post(9) if confirm '无人收货吗？'
        
    ,(e)-> 
        alert JSON.stringify(e)
        $rootScope.Back()
])