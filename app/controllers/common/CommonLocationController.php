<?php
/**
* LocationsController
*/
class CommonLocationController extends BaseController{
    public function getAll(){
        return $this->success('',Location::iall());
    }

    public function getSon($sid){
        $loc = Location::where('parent','=',$sid)->get();
        if (empty($loc)) {
            return $this->error('无法找到该地址');
        }else{
            return $this->success(Location::groupByLevel($loc));
        }
    }
}
