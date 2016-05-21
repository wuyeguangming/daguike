<?php
/**
* taobao api
*/
namespace common\taobao;
class api{
    // unicodeToUtf8
    static public function unicodeToUtf8( $str, $order = "little" ){
        $utf8string = "";
        $n = strlen( $str );
        $i = 0;
        for ( ; $i < $n; ++$i){
            if ( $order == "little" ){
                $val = str_pad( dechex( ord( $str[$i + 1] ) ), 2, 0, STR_PAD_LEFT ).str_pad( dechex( ord( $str[$i] ) ), 2, 0, STR_PAD_LEFT );
            }else{
                $val = str_pad( dechex( ord( $str[$i] ) ), 2, 0, STR_PAD_LEFT ).str_pad( dechex( ord( $str[$i + 1] ) ), 2, 0, STR_PAD_LEFT );
            }
            $val = intval( $val, 16 );
            ++$i;
            $c = "";
            if ( $val < 127 ){
                $c .= chr( $val );
            }else if ( $val < 2048 ){
                $c .= chr( 192 | $val / 64 );
                $c .= chr( 128 | $val % 64 );
            }else{
                $c .= chr( 224 | $val / 64 / 64 );
                $c .= chr( 128 | $val / 64 % 64 );
                $c .= chr( 128 | $val % 64 );
            }
            $utf8string .= $c;
        }
        if ( ord( substr( $utf8string, 0, 1 ) ) == 239 && ord( substr( $utf8string, 1, 2 ) ) == 187 && ord( substr( $utf8string, 2, 1 ) ) == 191 ){
            $utf8string = substr( $utf8string, 3 );
        }
        return $utf8string;
    }

    /**
     * 淘宝数据字段名
     *
     * @return array
     */
    private function fields(){
        return array(
            'name'         => '宝贝名称',
            'oldlevel'     => '新旧程度',
            'price'        => '宝贝价格',
            'num'          => '宝贝数量',
            'indate'       => '有效期',
            'express_fee'  => '运费承担',
            'recommend'    => '橱窗推荐',
            'body'         => '宝贝描述',
            'image'        => '新图片',
            // 'cid'       => '宝贝类目',
            // 'py_price'  => '平邮',
            // 'es_price'  => 'EMS',
            // 'kd_price'  => '快递',
            'is_show'      => '放入仓库',
        );
    }

    /**
     * 每个字段所在CSV中的列序号，从0开始算 
     *
     * @param array $title_arr
     * @param array $import_fields
     * @return array
     */
    private function fields_cols($title_arr, $import_fields){
        $fields_cols = array();
        foreach ($import_fields as $k => $field){
            $pos = array_search($field, $title_arr);
            if ($pos !== false){
                $fields_cols[$k] = $pos;
            }
        }
        return $fields_cols;
    }

    /**
     * 解析淘宝助理CSV数据
     *
     * @param string $csv_string
     * @return string
     */
    public function csv_parse($csv_string){
        /* 定义CSV文件中几个标识性的字符的ascii码值 */
        define('ORD_SPACE', 32); // 空格
        define('ORD_QUOTE', 34); // 双引号
        define('ORD_TAB',    9); // 制表符
        define('ORD_N',     10); // 换行\n
        define('ORD_R',     13); // 换行\r

        /*             字段信息 */
        $import_fields = $this->fields(); // 需要导入的字段在CSV中显示的名称
        $fields_cols   = array(); // 每个字段所在CSV中的列序号，从0开始算
        $csv_col_num   = 0; // csv文件总列数
        $pos          = 0; // 当前的字符偏移量
        $status       = 0; // 0标题未开始 1标题已开始
        $title_pos    = 0; // 标题开始位置
        $records      = array(); // 记录集
        $field        = 0; // 字段号
        $start_pos    = 0; // 字段开始位置
        $field_status = 0; // 0未开始 1双引号字段开始 2无双引号字段开始
        $line         = 0; // 数据行号
        $len = strlen($csv_string);
        while($pos < $len){
            $t     = ord($csv_string[$pos]); // 每个UTF-8字符第一个字节单元的ascii码
            //$next  = ord($csv_string[$pos + 1]);
            //$next2 = ord($csv_string[$pos + 2]);
            //$next3 = ord($csv_string[$pos + 3]);
            $next  = ($pos + 1 >= $len) ? '' : ord($csv_string[$pos + 1]);
            $next2  = ($pos + 2 >= $len) ? '' : ord($csv_string[$pos + 2]);
            $next3  = ($pos + 3 >= $len) ? '' : ord($csv_string[$pos + 3]);
            if ($status == 0 && !in_array($t, array(ORD_SPACE, ORD_TAB, ORD_N, ORD_R))){
                $status = 1;
                $title_pos = $pos;
            }
            
            if ($status == 1){
                if ($field_status == 0 && $t== ORD_N){
                    static $flag = null;
                    if ($flag === null){
                        $title_str = substr($csv_string, $title_pos, $pos - $title_pos);
                        $title_arr = explode("\t", trim($title_str));
                        $fields_cols = $this->fields_cols($title_arr, $import_fields);
                        
                        if (count($fields_cols) != count($import_fields)){
                            return false;
                        }
                        $csv_col_num = count($title_arr); // csv总列数
                        $flag = 1;
                    }

                    if ($next == ORD_QUOTE){
                        $field_status = 1; // 引号数据单元开始
                        $start_pos = $pos = $pos + 2; // 数据单元开始位置(相对\n偏移+2)
                    }else{
                        $field_status = 2; // 无引号数据单元开始
                        $start_pos = $pos = $pos + 1; // 数据单元开始位置(相对\n偏移+1)
                    }
                    continue;
                }

                if($field_status == 1 && $t == ORD_QUOTE && in_array($next, array(ORD_N, ORD_R, ORD_TAB))){ // 引号+换行 或 引号+\t
                    $records[$line][$field] = addslashes(substr($csv_string, $start_pos, $pos - $start_pos));
                    $field++;
                    if ($field == $csv_col_num){
                        $line++;
                        $field = 0;
                        $field_status = 0;
                        continue;
                    }
                    if (($next == ORD_N && $next2 == ORD_QUOTE) || ($next == ORD_TAB && $next2 == ORD_QUOTE) || ($next == ORD_R && $next2 == ORD_QUOTE)){
                        $field_status = 1;
                        $start_pos = $pos = $pos + 3;
                        continue;
                    }
                    if (($next == ORD_N && $next2 != ORD_QUOTE) || ($next == ORD_TAB && $next2 != ORD_QUOTE) || ($next == ORD_R && $next2 != ORD_QUOTE)){
                        $field_status = 2;
                        $start_pos = $pos = $pos + 2;
                        continue;
                    }
                    if ($next == ORD_R && $next2 == ORD_N && $next3 == ORD_QUOTE){
                        $field_status = 1;
                        $start_pos = $pos = $pos + 4;
                        continue;
                    }
                    if ($next == ORD_R && $next2 == ORD_N && $next3 != ORD_QUOTE){
                        $field_status = 2;
                        $start_pos = $pos = $pos + 3;
                        continue;
                    }
                }

                if($field_status == 2 && in_array($t, array(ORD_N, ORD_R, ORD_TAB))) { // 换行 或 \t
                    $records[$line][$field] = addslashes(substr($csv_string, $start_pos, $pos - $start_pos));
                    $field++;
                    if ($field == $csv_col_num){
                        $line++;
                        $field = 0;
                        $field_status = 0;
                        continue;
                    }
                    if (($t == ORD_N && $next == ORD_QUOTE) || ($t == ORD_TAB && $next == ORD_QUOTE) || ($t == ORD_R && $next == ORD_QUOTE)){
                        $field_status = 1;
                        $start_pos = $pos = $pos + 2;
                        continue;
                    }
                    if (($t == ORD_N && $next != ORD_QUOTE) || ($t == ORD_TAB && $next != ORD_QUOTE) || ($t == ORD_R && $next != ORD_QUOTE)){
                        $field_status = 2;
                        $start_pos = $pos = $pos + 1;
                        continue;
                    }
                    if ($t == ORD_R && $next == ORD_N && $next2 == ORD_QUOTE){
                        $field_status = 1;
                        $start_pos = $pos = $pos + 3;
                        continue;
                    }
                    if ($t == ORD_R && $next == ORD_N && $next2 != ORD_QUOTE){
                        $field_status = 2;
                        $start_pos = $pos = $pos + 2;
                        continue;
                    }
                }
            }

            if($t > 0 && $t <= 127) {
                $pos++;
            } elseif(192 <= $t && $t <= 223) {
                $pos += 2;
            } elseif(224 <= $t && $t <= 239) {
                $pos += 3;
            } elseif(240 <= $t && $t <= 247) {
                $pos += 4;
            } elseif(248 <= $t && $t <= 251) {
                $pos += 5;
            } elseif($t == 252 || $t == 253) {
                $pos += 6;
            } else {
                $pos++;
            }   
        }
        $return = array();
        foreach ($records as $key => $record){
            foreach ($record as $k => $col){
                $col = trim($col); // 去掉数据两端的空格
                /* 对字段数据进行分别处理 */
                switch ($k){
                    case $fields_cols['body']             : $return[$key]['body']            = str_replace(array("\\\"\\\"", "\"\""), array("\\\"", "\""), $col); break;
                    case $fields_cols['image']            : $return[$key]['image']           = trim($col,'"');break;
                    case $fields_cols['is_show']          : $return[$key]['is_show']         = $col == 1 ? 0 : 1; break;
                    case $fields_cols['name']             : $return[$key]['name']            = $col; break;
                    case $fields_cols['num']              : $return[$key]['num']             = $col; break;
                    case $fields_cols['price']            : $return[$key]['price']           = $col; break;
                    case $fields_cols['recommend']        : $return[$key]['recommend']       = $col; break;
                    // case $fields_cols['sale_attr']     : $return[$key]['sale_attr']       = $col; break;
                    case $fields_cols['oldlevel']         : $return[$key]['oldlevel']        = $col; break;
                    case $fields_cols['express_fee']      : $return[$key]['express_fee']     = $col; break;
                    // case $fields_cols['py_price']      : $return[$key]['py_price']        = $col; break;
                    // case $fields_cols['es_price']      : $return[$key]['es_price']        = $col; break;
                    // case $fields_cols['kd_price']      : $return[$key]['kd_price']        = $col; break;
                    //case $fields_cols['goods_indate']   : $return[$key]['goods_indate']    = $col; break;
                }
            }
        }
        return $return;
    }


    /**
     * 分割图片字符串
     *
     * @param string $pic_string 图片字符串
     * @return array 数组格式的返回内容
     */
    public function get_goods_image($pic_string){
        if($pic_string == ''){
            return false;
        }
        $pic_array = explode(';',$pic_string);
        if(!empty($pic_array) && is_array($pic_array)){
            $array  = array();
            $image    = array();
            $multi_image    = array();
            $i=0;
            foreach($pic_array as $v){
                if($v != ''){
                    $line = explode(':',$v);//[0] 文件名tbi [2] 排序
                    $image[] = $line[0];
                }
            }
            $array['image']   = $image;
            return $array;
        }else{
            return false;
        }
    }

    // public function imagesToBody($images){
    //     return join(';',$images);
    // }
}
