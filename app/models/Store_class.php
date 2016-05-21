<?php

class Store_class extends Eloquent {
    // /**
    //  * 构造检索条件
    //  *
    //  * @param int $id 记录ID
    //  * @return string 字符串类型的返回结果
    //  */
    // private function _condition($condition){
    //     $condition_str = '';
    //     if ($condition['parent_id'] != ''){
    //         $condition_str .= " and parent_id = '". intval($condition['parent_id']) ."'";
    //     }
    //     if ($condition['no_id'] != ''){
    //         $condition_str .= " and id != '". intval($condition['no_id']) ."'";
    //     }
    //     if ($condition['name'] != ''){
    //         $condition_str .= " and name = '". $condition['name'] ."'";
    //     }
    //     return $condition_str;
    // }

    // /**
    //  * 类别列表
    //  *
    //  * @param array $condition 检索条件
    //  * @return array 数组结构的返回结果
    //  */
    // public function getClassList($condition){
    //     $condition_str = $this->_condition($condition);
    //     $param = array();
    //     $param['table'] = 'store_class';
    //     $param['order'] = $condition['order'] ? $condition['order'] : 'parent_id asc,sort asc,id asc';
    //     $param['where'] = $condition_str;
    //     $result = Db::select($param);
    //     return $result;
    // }


    /**
     * 类别列表
     *
     * @return array 数组结构的返回结果
     */
    public function getClassList($condition){
        $result = DB::table('store_class')
            ->orderBy('parent_id', 'asc')
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return $result;
    }
    /**
     * 递归 整理分类
     *
     * @param int $show_deep 显示深度
     * @param array $class_list 类别内容集合
     * @param int $deep 深度
     * @param int $parent_id 父类编号
     * @param int $i 上次循环编号
     * @return array $show_class 返回数组形式的查询结果
     */
    private function _getTreeClassList($show_deep,$class_list,$deep=1,$parent_id=0,$i=0){
        static $show_class = array();//树状的平行数组
        if(is_array($class_list) && !empty($class_list)) {
            $size = count($class_list);
            if($i == 0) $show_class = array();//从0开始时清空数组，防止多次调用后出现重复
            for ($i;$i < $size;$i++) {//$i为上次循环到的分类编号，避免重新从第一条开始
                $val = $class_list[$i];
                $id = $val['id'];
                $parent_id   = $val['parent_id'];
                if($parent_id == $parent_id) {
                    $val['deep'] = $deep;
                    $show_class[] = $val;
                    if($deep < $show_deep && $deep < 2) {//本次深度小于显示深度时执行，避免取出的数据无用
                        $this->_getTreeClassList($show_deep,$class_list,$deep+1,$id,$i+1);
                    }
                }
                if($parent_id > $parent_id) break;//当前分类的父编号大于本次递归的时退出循环
            }
        }
        return $show_class;
    }

    /**
     * 取分类列表，按照深度归类
     *
     * @param int $show_deep 显示深度
     * @return array 数组类型的返回结果
     */
    public function getTreeClassList($show_deep='2', $condition = array()){
        $class_list = $this->getClassList($condition);
        $show_deep = intval($show_deep);
        $result = array();
        if(is_array($class_list) && !empty($class_list)) {
            $result = $this->_getTreeClassList($show_deep,$class_list);
        }
        return $result;
    }
}
