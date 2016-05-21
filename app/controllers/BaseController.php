<?php

class BaseController extends PublicController {
    public function __construct(){
        $this->beforeFilter('csrf', array('on' => 'post'));
        parent::__construct();
    }
}