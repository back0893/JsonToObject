<?php
/**
 * @author 刘国君
 * @version 1.0
 */

namespace liu;
function getValueType($value): string
{
    if (is_int($value)) {
        $type = 'int';
    } elseif (is_string($value)) {
        $type = 'string';
    } elseif (is_float($value)) {
        $type = 'float';
    } elseif (is_bool($value)) {
        $type = 'bool';
    } elseif (is_array($value)) {
        //这里需要判断是key->value字典
        //还是数组
        if (isset($value[0])) {
            $type = 'array';
        } else {
            $type = 'object';
        }
    }
    elseif (is_object($value)){
        $type = 'object';
    }
    else{
        var_dump(is_array($value),$value);
        $type='未知';
    }

    return $type;
}

function showDoc($request_param,MarkParam $markParam){
    $request=new \liu\Requests($request_param['url']);
    $param=$request_param['param'];
    $param['page_content']= $markParam->markdown();
    $request->setParam($param);
    $request->setMethod('post');
    $request->request();
}