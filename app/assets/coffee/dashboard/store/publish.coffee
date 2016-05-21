angular.module('dashboard')
.controller('publish', ['$scope', '$rootScope','http','commonService','commonAlbum','commonUploader','commonModal','$timeout','$routeParams','$location',($scope, $rootScope, http, commonService,commonAlbum,commonUploader,commonModal,$timeout,$routeParams,$location)->
    # TODO: 模式切换后数据检查，避免交叉
    $scope.NAME_MIN             = 0
    $scope.NAME_MAX             = 30
    $scope.SKU_DEFAULT         = {value:'',serial:'',price:0.01,volume:0,num:1,name:''}
    $scope.SKU_NAME_MIN        = 1
    $scope.SKU_NAME_MAX        = 10
    $scope.SKU_PRICE_MIN       = 0.01
    $scope.SKU_PRICE_MAX       = 100000000
    $scope.SKU_NUM_MIN         = 0
    $scope.SKU_NUM_MAX         = 100000000
    $scope.SKU_SERIAL_MIN      = 0
    $scope.SKU_SERIAL_MAX      = 30
    $scope.SKU_VOLUEM_MIN      = 0
    $scope.SKU_VOLUEM_MAX      = 3
    $scope.SKU_VOLUEM          = [$scope.SKU_VOLUEM_MIN..$scope.SKU_VOLUEM_MAX]
    $scope.HOURS                = [0..23]
    $scope.MINS                 = [0..59]
    
    $scope.publish_type         = 0
    $scope.files                = []
    $scope.files_upload_show    = '请选择数据包'
    # $scope.images_upload        = []
    $scope.images_upload_show   = '请选择图片'
    $scope.selected             = {}
    $scope.images_preview       = []
    $scope.buy_limit = 0
    $scope.is_show = 1

    $scope.filesSelected        = ($files, $event)->
        return if !$files.length
        $scope.files = $files # 注意两者有区别
        $scope.files_upload_show = $scope.files[0].name
    $scope.add_sku = ->
        $scope.skus.push angular.copy($scope.SKU_DEFAULT)
        # $scope.skus = $scope.skus.uniq()

    $scope.imagesSelected = ($images, $event)->
        return if !$images.length
        image_news = $images.copy() # 复制，防止关联
        for image in $images
            res = _.find($scope.images_preview, (i)-> return ((i.image.name == image.name) or (i.src.file_name() == image.name)))
            image_news.remove(image) if res? #确保文件名唯一性 
            #?? return commonModal.alert("同名文件冲突，请重命名："+res.src.file_name()) 
            # (image_news.remove(image) if _.isEqual(img, image)) for img in $scope.images_upload
        return if !image_news.length

        onload = (image)->
            fileReader.onload = (e) -> 
                if $scope.images_preview.length
                    max = _.max($scope.images_preview, (images_preview)-> return images_preview.order)
                else
                    max = {order:0}
                $scope.images_preview.push
                    image: image # 
                    order: max.order+1 # 为了防止拖拽后顺序变化导致删除顺序与显示不一致
                    src: e.target.result
                $scope.$apply() # 通知ng更新
        # 先重整order
        for image in image_news
            # $scope.images_upload.push(image)
            fileReader = new FileReader()
            fileReader.readAsDataURL image
            onload image

        $scope.images_upload_show  = ''
        ($scope.images_upload_show += ','+image.name) for image in $scope.images_preview when image.image.name?
        $scope.images_upload_show  = $scope.images_upload_show[1..-1]
    $scope.image_remove = (image)->
        # $scope.images_upload.remove(image.image)
        $scope.images_preview.remove(image)    

    $scope.sale_map_sel = (item)->
        if $scope.selected.sale_map_item not in $scope.selected.sale_map
            $scope.selected.sale_map.push $scope.selected.sale_map_item
        else
            $scope.sale_map_alert = item+'已添加'
            $timeout (->$scope.sale_map_alert = ''), 3000
        $scope.selected.sale_map_item = ''

    commonService.init 'publish',->
        if $routeParams.id and !data.goods
            return commonModal.alert '无法找到该商品，或该商品已被删除！', ->
                return $location.path('/dashboard/store/publish')
        goods                    = $rootScope.data.goods or {} # edit or new
        $scope.categories        = commonAlbum.b2f($rootScope.data.categories) 
        $scope.albums               = commonAlbum.b2f($rootScope.data.albums) 
        $scope.id                   = goods.id or 0
        $scope.name                 = goods.name or ''
        $scope.subtitle             = goods.subtitle or ''
        $scope.buy_limit            = goods.buy_limit or 0
        $scope.is_show              = if goods.is_show? then goods.is_show else 1
        $scope.selected.category = goods.category or {}
        $scope.selected.album       = goods.album       or {}

        $scope.selected.sale_map = []
        $scope.selected.sale_map_item = ''
        $scope.sale_map = []
        $scope.sale_map.push loc.name for loc in $rootScope.data.locs

        if goods.image? and goods.body?
            for img,order in _.without(_.union([goods.image], goods.body.split(';')),'')
                $scope.images_preview.push
                    image: {}
                    order: order
                    src: U.img(img,'sm')
        $scope.skus = goods.skus || [angular.copy($scope.SKU_DEFAULT)]
        start_time        = if goods.start_time then new XDate(goods.start_time*1000) else new XDate()
        end_time          = if goods.end_time then new XDate((goods.end_time )*1000) else (new XDate()).addYears(1)
        $scope.start_hour = start_time.getHours()
        $scope.start_min  = start_time.getMinutes()
        $scope.end_hour   = end_time.getHours()
        $scope.end_min    = end_time.getMinutes()
        $scope.now_time   = new XDate().toString("yyyy-MM-dd")
        $scope.start_time = start_time.toString("yyyy-MM-dd")
        $scope.end_time   = end_time.toString("yyyy-MM-dd")
        
        $scope.valid_new     = -> !$scope.publish_type and $scope.images_preview.length and $scope.selected.category.id and $scope.selected.album.id 
        $scope.valid_import  = -> $scope.publish_type and $scope.selected.category.id and $scope.selected.album.id

        $scope.submit        = -> 
            return commonModal.alert('限购数量目前不能超过1') if $scope.buy_limit > 1
            $scope.btn_info = '提交中，请稍后...'
            body = []
            images_upload = []
            for images_preview in $scope.images_preview
                body.push (if(images_preview.image.name?) then images_preview.image.name else images_preview.src).file_name()
                images_upload.push images_preview.image if images_preview.image.name?
            files = _.union($scope.files, images_upload)
            # $scope.skus = $scope.skus.uniq() //todo: 去重
            skus = []
            for sku in $scope.skus
                (return commonModal.alert('价格 必须大于0！') ) if !sku.price? #!sku.num? or 
                skus.push(_.omit(sku, '$$hashKey')) 
            start_time = XDate($scope.start_time)
            start_time.setHours($scope.start_hour)
            start_time.setMinutes($scope.start_min)
            end_time = XDate($scope.end_time)
            end_time.setHours($scope.end_hour)
            end_time.setMinutes($scope.end_min)
            commonUploader.upload
                url: '/dashboard/store/publish'
                file: files
                fileFormDataName: _.map(files, (list)-> list.name)
                data: 
                    data: JSON.stringify # data: #todo: upload bug ->JSON.stringify
                        id: $scope.id
                        publish_type: $scope.publish_type
                        name: $scope.name
                        subtitle: $scope.subtitle
                        skus: skus
                        start_time: start_time
                        end_time: end_time
                        category: $scope.selected.category
                        album: $scope.selected.album
                        body: body.join(';')
                        sale_map: $scope.selected.sale_map
                        buy_limit: $scope.buy_limit
                        is_show: $scope.is_show
            ,(data)->
                $scope.btn_info = '提交' 
                commonModal.alert '提交成功！'
            ,(data)->
                $scope.btn_info = '提交' 
                commonModal.alert '提交失败！'
                console.log data.info
])
