<?php
/**
 * @author 刘国君
 * @version 1.0
 *  ['参数名','必选','类型','说明'];
 */

require_once __DIR__.'/TestA.php';
interface export
{
    function markdown(): string;
}

class Mark implements export
{
    protected $title;
    protected $value;

    /**
     * Mark constructor.
     * @param string $title
     * @param export $value
     */
    public function __construct(string $title, export $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    function markdown(): string
    {
        $markdown = sprintf("**%s:**\n", $this->title);
        $markdown .= $this->value->markdown();
        return $markdown;
    }

}

/**
 * Class MarkTitle
 *队列
 */
class MarkList implements export
{
    protected $list;

    /**
     * MarkList constructor.
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    function markdown(): string
    {
        $makrdown = [];
        foreach ($this->list as $item) {
            $makrdown[] = sprintf("- %s", $item);
        }
        return implode("\n", $makrdown);
    }

}

/**
 * Class MarkForm
 * 表格
 */
class MarkForm implements export
{
    protected $filed = ['参数名', '必选', '类型', '说明'];
    protected $value;

    /**
     * MarkForm constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    function markdown(): string
    {
        $markdown = [];
        $markdown[] = implode('|', $this->filed);
        $markdown[] = implode('|', array_fill(0, ':----', count($this->filed)));
        $fileds=getValueType($this->value);
        foreach ($fileds as $filed){
            $markdown[]=implode('|',$filed);
        }
        return implode("\n", $markdown);
    }

}

/**
 * Class MarkCode
 * 代码
 */
class MarkCode implements export
{
    protected $code;

    /**
     * MarkCode constructor.
     * @param $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    function markdown(): string
    {
        $markdown = '```';
        $markdown .= $this->code;
        $markdown .= '```';
        return $markdown;
    }


}


/**
 * Class MarkParam
 * @property string $url
 * @property array method
 * @property array $formData
 */
class MarkParam implements export
{
    protected $url; //请求的url
    protected $method; //请求的方法
    protected $formData; //请求的内容
    protected $response;

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }//返回值


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getMethod(): array
    {
        return $this->method;
    }

    /**
     * @param array $method
     */
    public function setMethod(array $method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @param array $formData
     */
    public function setFormData(array $formData): void
    {
        $this->formData = $formData;
    }

    function markdown(): string
    {
        $markdown = [];
        $markdown[] = (new Mark('请求url', new MarkList([$this->url])));
        $markdown[] = (new Mark('请求方法', new MarkList([$this->url])));
        $markdown[] = (new Mark('参数', new MarkForm($this->formData)));
        $markdown[] = (new Mark('返回参数', new MarkForm($this->response)));
        $markdown[] = (new Mark('返回实例', new MarkCode($this->response)));
    }
}

/**
 * 获得记录类型

 */

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

//从json映射到对象中
//对象的注释必须是 @var 类型 名称 说明
//注释没有办法约束,,7.4导致可以,但是太新了..
//好多不支持..

function mapJson($json,&$object){
    $reflect=new ReflectionObject($object);
    $properties=$reflect->getProperties(ReflectionProperty::IS_PUBLIC);
    $parameters=$reflect->getConstructor()->getParameters();
    $types=[];
    foreach ($parameters as $parameter){
        $types[$parameter->getName()]=$parameter->getType()->getName();
    }
    foreach ($properties as $property){
        $name=$property->getName();
        $type=$types[$name]??'';
        $jsonValue=$json->$name??null;
        if(is_null($jsonValue)){
            throw new Exception("返回错误值null");
        }
        $jsonType=getValueType($jsonValue);
        if($jsonType=='object'){
            //如果是一个对象.
            $class=new ReflectionClass($type);
            $instance=$class->newInstanceWithoutConstructor();
            mapJson($jsonValue,$instance);
            $property->setValue($object,$instance);
            continue;
        }
        if($type!=$jsonType){
            throw new Exception(sprintf("%s的类型错误,返回类型%s!,期望类型%s",$property->getName(),$jsonType,$type));
        }
        $property->setValue($object,$jsonValue);
    }
}



$a=\example\TestA::instance();
$json=new stdClass();
$json->name='liu';
$json->password='123456';
$json->age=123;
$json->double=1.234;
$json->testB=new stdClass();
$json->testB->lists=[1,3,4,5];
mapJson($json,$a);
var_dump($a);