angular.module('wx')
.controller('about', ['$rootScope',($rootScope) ->
    $rootScope.wx_navbar =
        id: 2
        ready: true
        btn: [ 
            text: '返回'
            class: 'btn-default'
        ]
])