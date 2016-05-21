angular.module('dashboard')
.controller('album', ['$scope', '$rootScope','http','commonService','commonModal','commonAlbum',($scope, $rootScope, http, commonService, commonModal,commonAlbum) ->
    commonService.init 'album',->
        $scope.albums       = commonAlbum.b2f($rootScope.data.albums)
        $scope.MAX_DEPTH    = 3
        $scope.MAX_LEN      = 20
        $scope.selectedItem = {}
        $scope.deletes      = []
        $scope.options      = 
            accept: (sourceNode, destNodes, destIndex) -> #确保深度
                if destNodes.depth() and (destNodes.depth() + sourceNode.maxSubDepth() + 1)>$scope.MAX_DEPTH then false else true
            dropped:(event)->
                dest_scope = event.dest.nodesScope
                event.source.nodeScope.$modelValue.parent_id = if dest_scope.$nodeScope then dest_scope.$nodeScope.$modelValue.id else 0
                event.source.nodeScope.$modelValue.is_changed = true
        $scope.del = (scope) -> 
            commonModal.confirm '删除专辑会删除该专辑内的商品，确定删除吗？',->
                return commonModal.alert('至少一个专辑！') if ((1==$scope.albums.length) and (0==scope.$modelValue.parent_id))
                del = (node)->
                    $scope.deletes.push(node.id) if !node.is_new
                    del(item) for item in node.items
                del(scope.$modelValue)
                scope.remove()
        $scope.toggle = (scope) -> scope.toggle()
        $scope.edit   = (scope) -> 
            commonModal.prompt '修改专辑名称',('长度范围1~'+$scope.MAX_LEN),('/^.{1,'+$scope.MAX_LEN+'}$/'),scope.$modelValue.title,(value)-> 
                scope.$modelValue.title      = value
                scope.$modelValue.name       = value
                scope.$modelValue.is_changed = true
        $scope.addSubItem = (scope)->
            return commonModal.alert ('专辑深度不能超过'+$scope.MAX_DEPTH+'层') if scope.depth() >=$scope.MAX_DEPTH
            commonModal.prompt '',('长度范围1~'+$scope.MAX_LEN),('/^.{1,'+$scope.MAX_LEN+'}$/'),'新专辑',(title)-> 
                nodeData = scope.$modelValue
                nodeData.items.push
                    store_id:  $rootScope.data.user.store.id
                    id:        $rootScope.Uuid()
                    parent_id: nodeData.id
                    name:      title
                    title:     title
                    items:     []
                    is_new:    true
                    sort:      0
        $scope.addGroup = (scope)->
            commonModal.prompt '',('长度范围1~'+$scope.MAX_LEN),('/^.{1,'+$scope.MAX_LEN+'}$/'),'新专辑',(title)-> 
                $scope.albums.push
                    store_id:  $rootScope.data.user.store.id
                    id:        $rootScope.Uuid()
                    parent_id: 0
                    name:      title
                    title:     title
                    items:     []
                    is_new:    true
                    sort:      0
        getRootNodesScope = -> angular.element(document.getElementById("tree-root")).scope()
        $scope.expand     = -> getRootNodesScope().expandAll()
        $scope.collapse   = -> getRootNodesScope().collapseAll()
        $scope.save       = ->
            albums = commonAlbum.f2b($scope.albums)
            return commonModal.alert('专辑未发生变化，无需保存！') if _.isEmpty(albums) and !$scope.deletes.length
            $rootScope.btn_info       = '提交中，请稍后...'
            albums.deletes = $scope.deletes
            http.post '/dashboard/store/album',
                albums: albums
            .then (res)->
                $scope.albums = commonAlbum.b2f(res.data)
                $rootScope.btn_info = '保存' 
                commonModal.alert('保存成功！')
            ,(e)->
                $rootScope.btn_info = '保存' 
                commonModal.alert(e)
])
.controller('albumEdit',($scope,$modalInstance,modal)-> #(type)->
    $scope.type = 'prompt'
    $scope.ok   = (data)->
      $modalInstance.close(data)
    $scope.cancel = ->
      $modalInstance.dismiss('cancel')
)
