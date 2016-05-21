<?php
class StoreIndexController extends BaseController {
    /**
     * Store Model
     * @var Store
     */
    protected $Store;

    /**
     * Inject the models.
     * @param Store $Store
     */
    public function __construct(Store $Store){
        parent::__construct();
        $this->store = $Store;
        $this->__path__ = 'site/store/index';
    }


    public function getIndex($store){
        dd($store);
    }
}
