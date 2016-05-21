# commonModal.prompt('xxx').result.then
angular.module('common')

# prompt
.controller('commonModalPrompt',['$scope','$modalInstance','title','model','title_rule','rule','success','error',($scope,$modalInstance,title,model,title_rule,rule,success,error)->
    $scope.type       = 'prompt'
    $scope.title      = title
    $scope.model      = model
    $scope.title_rule = title_rule
    $scope.rule       = -> if $scope.model then eval(rule).test($scope.model) else false
    $scope.ok = -> 
        $modalInstance.close()
        success($scope.model) if success
    $scope.cancel = -> 
        $modalInstance.dismiss()
        error() if error
])

# alert
.controller('commonModalAlert',['$scope','$modalInstance','title','success','error',($scope,$modalInstance,title,success,error)->
    $scope.type = 'alert'
    $scope.title = title
    $scope.cancel = -> 
        $modalInstance.close()
        success($scope.model) if success
])

# confirm
.controller('commonModalConfirm',['$scope','$modalInstance','title','success','error',($scope,$modalInstance,title,success,error)->
    $scope.type = 'confirm'
    $scope.title = title
    $scope.ok = -> 
        $modalInstance.close()
        success($scope.model) if success
    $scope.cancel = -> 
        $modalInstance.close()
        error() if error
])

.factory('commonModal', ['$modal', ($modal)->
    prompt: (title,title_rule,rule,model,success,error)->
        return $modal.open
            templateUrl: 'common/base/modal.html'
            controller: 'commonModalPrompt'
            size: 'sm'
            resolve:
                title:      -> return title
                model:      -> return model
                title_rule: -> title_rule
                rule:       -> rule
                success:    -> return success if success
                error:      -> return error if error
        
    alert: (title,success,error)->
        return $modal.open
            templateUrl: 'common/base/modal.html'
            controller: 'commonModalAlert'
            size: 'sm'
            resolve:
                title:   -> return title if title
                success: -> return success if success
                error:   -> return error if error
    confirm: (title,success,error)->
        return $modal.open
            templateUrl: 'common/base/modal.html'
            controller: 'commonModalConfirm'
            size: 'sm'
            resolve:
                title:   -> return title if title
                success: -> return success if success
                error:   -> return error if error
])
