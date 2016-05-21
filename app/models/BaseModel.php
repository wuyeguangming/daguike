<?php
use LaravelBook\Ardent\Ardent;
class BaseModel extends Ardent{
    // 借鉴TP的自动完成 >>>>
    const MODEL_INSERT          =   1;      //  插入模型数据
    const MODEL_UPDATE          =   2;      //  更新模型数据
    const MODEL_BOTH            =   3;      //  包含上面两种方式
    // protected $_map             =   array();  // 字段映射定义
    // protected $_scope           =   array();  // 命名范围定义
    protected $_validate        =   array();  // 自动验证定义
    protected $_auto            =   array();  // 自动完成定义

    /**
     * 自动完成需要存入数据的格式化
     * @param  [array obj] $data
     * @return [array]      
     */
    private function _TP_auto($data) {
        $data = (array)$data;
        // 状态
        $type = (empty($this->id) && empty($data['id']))?  self::MODEL_INSERT : self::MODEL_UPDATE; //todo: 正确??
        if(!empty($this->_auto)) {
            // 以下来自TP core.model
            foreach ($this->_auto as $auto){
                // 填充因子定义格式
                // array('field','填充内容','填充条件','附加规则',[额外参数])
                if(empty($auto[2])) $auto[2] = self::MODEL_INSERT; // 默认为新增的时候自动填充
                if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {
                    switch(trim($auto[3])) {
                        case 'function':    //  使用函数进行填充 字段的值作为参数
                        case 'callback': // 使用回调方法
                            $args = isset($auto[4])?(array)$auto[4]:array();
                            if(isset($data[$auto[0]])) {
                                array_unshift($args,$data[$auto[0]]);
                            }
                            if('function'==$auto[3]) {
                                $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                            }else{
                                $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                            }
                            break;
                        case 'field':    // 用其它字段的值进行填充
                            $data[$auto[0]] = $data[$auto[1]];
                            break;
                        case 'ignore': // 为空忽略
                            if(''===$data[$auto[0]])
                                unset($data[$auto[0]]);
                            break;
                        case 'string':
                        default: // 默认作为字符串填充
                            $data[$auto[0]] = $auto[1];
                    }
                    if(false === $data[$auto[0]] )   unset($data[$auto[0]]);
                }
            }
        }
        return $data;
    }
    // <<<<

    /**
     * 判断是否保存成功true，否则获取错误$this->errors()
     * @param  [obj or array]
     * @return [bool]
     */
    public function iSave($data){
        $data = $this->_TP_auto($data);
        // $data = array_intersect_key($data, $this->getAttributes()); // todo: 过滤非法属性
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
        return $this->save(); // 获取errors: $this->errors()
    }

    /**
     * 复制$data的属性，并新建实例
     * @param  [obj or array]
     * @return [bool]
     */
    public function iCreate($data){
        return $this->iSave($data);
    }

    /**
     * 改造为适合angular的分页
     * @param  [obj]  $query  
     * @param  integer $page   
     * @param  integer $perPage
     * @return [array]
     */
    public function iPage($query, $page = 1, $perPage = 10, $sort = 'id', $is_desc=false){
        $res = $this->where($query);
        $total    = $res->count();
        $last_page = (int) ceil($total / $perPage);
        return array(
            'total'        => $total, 
            'per_page'     => $perPage,
            'current_page' => intval($page), 
            'last_page'    => ($last_page >= 1) ? $last_page : 1,
            'data'         => $res->orderBy($sort, $is_desc?'desc':'asc')->skip($perPage * ($page - 1))->take($perPage)->get()->toArray()
        );
    }
}