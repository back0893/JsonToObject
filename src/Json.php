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


