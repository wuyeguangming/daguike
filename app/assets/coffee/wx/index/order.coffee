angular.module('wx')
.filter 'orderAmount',-> 
    return (order)->  
        return (if (parseFloat(order.discount) >= 0.01) then ('优惠'+order.discount+'元 ') else '') + '合计' + order.amount + '元'
.filter 'orderStatus',-> 
    return (order)-> 
        return if !order?
        # todo 发货中->正在配货
        return ['已取消','等待支付','下单成功','发货中','已发货','交易成功','申请退款中','退款成功','退款失败','无人收货'][order.status]
.controller('orderIndex', ['$scope', '$rootScope' ,'commonService','http','$filter','$location',($scope, $rootScope, commonService, http, $filter,$location) ->
    data.order = []
    $rootScope.wx_navbar =
        id: 2
        ready: false
        btn: [
            text: '首页'
            hide: -> !data.order
            click: -> $location.path('/')
        ,
            hide: -> !data.order
        ]
    $scope.index =
        title : '所有订单'
        btn_more: '点击查看更多'
        order : []
        order_goods : []
        update: ()-> 
            $scope.index.order.push order for order in data.order
            $scope.index.order_goods.push order_goods for order_goods in data.order_goods
            for order_goods,index in $scope.index.order_goods
                $scope.index.order[index].order_goods = []
                for i in order_goods
                    item =
                        image: $rootScope.U.img(i.goods_image,'sm')
                    item.p = [i.goods_name,$filter('currency')(i.sku_price,"￥"),i.sku_value,'X'+i.num]
                    item.class = []
                    $scope.index.order[index].order_goods.push item
        click: (select)-> $rootScope.Goto('/wx/order/detail/'+select.id)
        pull:-> 
            $scope.index.btn_more = '加载中，请稍后...'
            http.get('/wx/order/index/'+($scope.index.order.length)).then (ret)->
                window.data = ret
                $scope.index.btn_more = if ret.order.length then '点击查看更多' else '没有更多的了'
                $scope.index.update()
            ,(e)->$scope.index.btn_more = '点击查看更多'
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
            $rootScope.wx_navbar.btn[2].text = '订单确认中...'
            $rootScope.wx_navbar.btn[2].click = ''
            $timeout ->
                if pay_confirm_cnt++ > 10
                    pay_confirm_cnt = 0
                    $rootScope.wx_navbar.btn[2].click = -> pay_confirm(id)
                    return $rootScope.wx_navbar.btn[2].text = '确认超时' 
                http.get('/wx/order/detail/'+id).then (ret)->
                    return pay_confirm(id) if (!ret.order? or ret.order.status == 1)
                    $rootScope.data = ret
                    $rootScope.wx_navbar.btn[2].class = 'btn-success'
                    $rootScope.wx_navbar.btn[2].text = '下单成功！'
                    $rootScope.wx_navbar.btn[2].click = -> $rootScope.Goto('/')
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
        if 1 <= parseInt(data.order.status) <= 2 #and !data.order.pay_type
            $rootScope.wx_navbar.btn[1] =
                text: '取消订单'
                class: 'btn-default'
                click: ->
                    if confirm '确定取消订单吗？'
                        $rootScope.wx_navbar.btn[1].text = '提交中，请稍后'
                        http.post('/wx/order/cancel/'+data.order.id).then (res)->
                            data.order.status = 0
                            $rootScope.wx_navbar.btn[1].text = '订单已取消'
                            $rootScope.wx_navbar.btn.remove($rootScope.wx_navbar.btn[2])
                            $rootScope.wx_navbar.btn[1].click = -> $rootScope.Back()
                        ,(e)->$rootScope.wx_navbar.btn[1].text = e
            if !data.order.pay_type
                $rootScope.wx_navbar.btn[2] =
                    text: '立即支付'
                    class: ['btn-danger','btn-danger','btn-success'][data.order.status]
                    click: ->
                        $rootScope.wx_navbar.btn[2].text = '支付中，请稍后...'
                        return pay_confirm(data.order.id) if(data.order.pay_type or data.order.ifrom)
                        wx.ready ->
                            wx.chooseWXPay 
                                timestamp: data.wx.pay.timeStamp #支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                                nonceStr: data.wx.pay.nonceStr #支付签名随机串，不长于 32 位
                                package:  data.wx.pay.package #统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                                signType: data.wx.pay.signType #签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                                paySign:  data.wx.pay.paySign #支付签名
                                success:  (res) -> pay_confirm(data.order.id)
                                cancel: (res) ->
                                    $rootScope.wx_navbar.btn[2].text = '立即支付'
                                    $rootScope.$apply()
                        $rootScope.wx_config(data.wx)
                    # if data? then $rootScope.wx_config(data.wx) else $rootScope.wx_navbar.btn[1].text = '立即支付'
        # todo 付款后退单
        # 暂时不支持付款后退单，需要后台同意等协调，较为复杂
        # else if 2 == data.order.status
        #     $rootScope.wx_navbar.btn[1] =
        #         text: '取消订单'
        #         class: 'btn-default'
        #         click: ->
        #             if confirm '确定取消订单吗？'
        #                 $rootScope.wx_navbar.btn[1].text = '提交中，请稍后...'
        #                 http.post('/wx/order/cancel/'+data.order.id).then (res)->
        #                     data.order.status = 0
        #                     $rootScope.wx_navbar.btn[1].text = '订单已取消'
        #                     $rootScope.wx_navbar.btn[1].click = -> $rootScope.Back()
        #                 ,(e)->$rootScope.wx_navbar.btn[1].text = e
        # 退款
        else if 5 == data.order.status and 0 == data.order.pay_type
            $rootScope.wx_navbar.btn[1] =
                text: '申请退款'
                class: 'btn-danger'
                click: ->
                    if confirm '确定申请退款吗？'
                        $rootScope.wx_navbar.btn[1].text = '提交中，请稍后...'
                        http.post('/wx/order/refund/'+data.order.id).then (res)->
                            data.order.status = 6
                            $rootScope.wx_navbar.btn[1].text = '申请成功，等待退款！'
                            $rootScope.wx_navbar.btn[1].click = -> $rootScope.Back()
                        ,(e)->$rootScope.wx_navbar.btn[1].text = e
    ,(e)-> 
        alert JSON.stringify(e)
        $rootScope.Back()
])