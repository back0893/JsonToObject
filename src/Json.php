<?php

//从json映射到对象中
//对象的注释必须是 @var 类型 名称 说明
//注释没有办法约束,,7.4导致可以,但是太新了..
namespace liu;

class Json
{
    public function decode($json,string $class){
        $reflect=new \ReflectionClass($class);
        $object=$reflect->newInstanceWithoutConstructor();
        $properties=$reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $parameters=$reflect->getConstructor()->getParameters();
        $types=[];
        foreach ($parameters as $parameter){
            $types[$parameter->getName()]=$parameter->getType()->getName();
        }
        foreach ($properties as $property){
            $name=$property->getName();
            $type=$types[$name]??'';
            if(is_array($json)){
                $jsonValue=$json[$name]??null;
            }
            else{
                $jsonValue=$json->$name??null;
            }
            if(is_null($jsonValue)){
                throw new \Exception("返回错误值null");
            }
            $jsonType=getValueType($jsonValue);
            if($jsonType=='object'){
                //如果是一个对象.
                $property->setValue($object, $this->decode($jsonValue,$type));
                continue;
            }
            if($type!=$jsonType){
                throw new \Exception(sprintf("%s的类型错误,返回类型%s!,期望类型%s",$property->getName(),$jsonType,$type));
            }
            $property->setValue($object,$jsonValue);
        }
        return $object;
    }
}

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
