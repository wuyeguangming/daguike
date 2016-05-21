<?php
/**
* LocationsController
*/
class CommonCategoryController extends BaseController{
    public function getChildren(){
        return $this->result(Category::where('parent_id','=',Input::get('parent_id', '0'))->get());
    }
}
