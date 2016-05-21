angular.module('common')
.directive 'commonOption', ($rootScope) ->
    templateUrl: 'wx/common/option.html'
    scope:
        option: '='
    link:(scope, iElement, iAttrs) ->
        return if !scope.option?
        scope.filter = (v)-> if scope.option.filter? then scope.option.filter(v) else v
        scope.filter_subitem = (v)-> if scope.option.filter_subitem? then scope.option.filter_subitem(v) else ''
        if scope.option.checkbox
            scope.option.selects = []
            scope.option.selects.push i for i in scope.option.list
            scope.class = (item,index)-> 
                {true:'active',false:'inactive'}[scope.option.selects[index].select or false]
            scope.click = (item,index)-> 
                return if item.disenabled
                scope.option.selects[index].select = if scope.option.selects[index].select? then !scope.option.selects[index].select else true
                # scope.option.forward(scope.option.select) if scope.option.forward? && scope.option.select
                scope.option.click(item,index) if scope.option.click?
        else
            scope.click = (item,index)-> 
                return if item.disenabled
                scope.option.select = item
                scope.option.click(item,index) if scope.option.click?
            scope.class = (item,index)-> 
                {true:'active',false:'inactive'}[scope.option.select == item] + ' ' + (item.class or '')
.directive 'commonNavbar', ->
    templateUrl: 'wx/common/navbar.html'
    scope:
        navbar: '='
    link:(scope, iElement, iAttrs) ->
        scope.navbar = {} if !scope.navbar?
        scope.navbar.id = 1 if !scope.navbar.id?
        scope.navbar.btn = [{}] if !scope.navbar.btn?
        for btn in scope.navbar.btn
            btn.click = scope.$root.Back if !btn.click?
            btn.class = 'btn-default' if !btn.class?
            btn.text = '返回' if !btn.text?
.directive 'commonMedia', ->
    templateUrl: 'wx/common/media.html'
    scope:
        media: '='
    link:(scope, iElement, iAttrs) ->
.directive 'commonModal', ->
    templateUrl: 'wx/common/modal.html'
    scope:
        modal: '='
    link:(scope, iElement, iAttrs) ->
        scope.show = false